<?php

/**
 * @file
 * Migrates base64 inline images in formatted text fields to managed files.
 *
 * Usage (arguments go after "--"):
 * @code
 *   ddev drush php:script scripts/migrate_base64_images.php                   # dry run, all
 *   ddev drush php:script scripts/migrate_base64_images.php -- --nid=636      # dry run, one node
 *   ddev drush php:script scripts/migrate_base64_images.php -- --dry-run=0 --limit=20
 * @endcode
 *
 * Options:
 * - --dry-run[=1|0]: Default on. Pass --dry-run=0 (or --execute) to write.
 * - --nid=N: Only process this entity ID.
 * - --limit=N: Process at most N entities.
 *
 * For each img whose src is a data:image URI the image is decoded, stored
 * once per content hash as public://inline-images/<Y-m>/<sha1>.<ext> (scaled
 * down to 1920px wide when wider, SVG untouched), registered as a permanent
 * managed file, and the img is rewritten to reference it the way CKEditor 5
 * does (src, data-entity-type, data-entity-uuid, width, height). All other
 * attributes are kept. Images that fail to decode or save are logged and left
 * as they are. Each changed entity is saved once, as a new revision; file
 * usage is then recorded by the editor module and verified here.
 *
 * Re-running is safe: migrated images no longer match, and identical content
 * reuses the existing file.
 */

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\File\FileExists;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;
use Drupal\user\EntityOwnerInterface;

// Entity type / field pairs to scan. Add e.g.
// ['entity_type' => 'taxonomy_term', 'field' => 'description'] later.
const B64_TARGETS = [
  ['entity_type' => 'node', 'field' => 'body'],
];
const B64_MAX_WIDTH = 1920;
const B64_BASE_DIR = 'public://inline-images';
const B64_REVISION_LOG = 'Migrate base64 images to files';
const B64_EXTENSIONS = [
  'png' => 'png',
  'jpeg' => 'jpg',
  'jpg' => 'jpg',
  'gif' => 'gif',
  'webp' => 'webp',
  'svg+xml' => 'svg',
];

// --- Options ----------------------------------------------------------------
$options = ['dry-run' => TRUE, 'nid' => NULL, 'limit' => NULL];
foreach ($extra ?? [] as $arg) {
  if ($arg === '--execute') {
    $options['dry-run'] = FALSE;
  }
  elseif (preg_match('/^--dry-run(?:=(.*))?$/', $arg, $m)) {
    $options['dry-run'] = !isset($m[1]) || !in_array(strtolower($m[1]), ['0', 'false', 'no', 'off'], TRUE);
  }
  elseif (preg_match('/^--(nid|limit)=(\d+)$/', $arg, $m)) {
    $options[$m[1]] = (int) $m[2];
  }
  else {
    fwrite(STDERR, "Unknown argument: $arg\n");
    exit(1);
  }
}
$dryRun = $options['dry-run'];

$entityTypeManager = \Drupal::entityTypeManager();
$fileSystem = \Drupal::service('file_system');
$fileUrlGenerator = \Drupal::service('file_url_generator');
$fileUsage = \Drupal::service('file.usage');
$imageFactory = \Drupal::service('image.factory');
$mimeGuesser = \Drupal::service('file.mime_type.guesser');

// --- Log --------------------------------------------------------------------
$logDir = DRUPAL_ROOT . '/../scripts/logs';
if (!is_dir($logDir)) {
  mkdir($logDir, 0775, TRUE);
}
$logPath = sprintf('%s/base64-%s%s.csv', realpath($logDir), date('Ymd-His'), $dryRun ? '-dryrun' : '');
$log = fopen($logPath, 'w');
fputcsv($log, ['entity_type', 'id', 'langcode', 'original_bytes', 'new_bytes', 'images', 'files', 'errors'], escape: '');

$stats = [
  'entities' => 0,
  'translations' => 0,
  'images' => 0,
  'resized' => 0,
  'files_created' => 0,
  'files_reused' => 0,
  'freed_bytes' => 0,
  'new_file_bytes' => 0,
  'failed_images' => 0,
  'failed_entities' => 0,
];

/**
 * Finds an existing managed file for the hash, e.g. from an earlier run.
 */
function b64_existing_file(string $filename): ?FileInterface {
  $ids = \Drupal::entityQuery('file')
    ->accessCheck(FALSE)
    ->condition('filename', $filename)
    ->condition('uri', B64_BASE_DIR . '/', 'STARTS_WITH')
    ->range(0, 1)
    ->execute();
  return $ids ? File::load(reset($ids)) : NULL;
}

/**
 * Text used to check that rewriting only touched images.
 */
function b64_text_signature(\DOMDocument $doc): string {
  return preg_replace('/\s+/u', '', $doc->textContent);
}

printf("Mode: %s\n", $dryRun ? 'DRY RUN (nothing is written; pass --dry-run=0 to execute)' : 'EXECUTE');

// Files resolved during this run, keyed by filename, so identical content is
// saved once even in dry-run mode; plus their stored dimensions.
$seen = [];
$dimensions = [];

foreach (B64_TARGETS as $target) {
  $entityTypeId = $target['entity_type'];
  $fieldName = $target['field'];
  $storage = $entityTypeManager->getStorage($entityTypeId);
  $idKey = $storage->getEntityType()->getKey('id');

  $query = $storage->getQuery()
    ->accessCheck(FALSE)
    ->condition($fieldName . '.value', 'data:image/', 'CONTAINS')
    ->sort($idKey);
  if ($options['nid']) {
    $query->condition($idKey, $options['nid']);
  }
  if ($options['limit']) {
    $query->range(0, $options['limit']);
  }
  $ids = array_values($query->execute());
  printf("%s.%s: %d entities to process\n", $entityTypeId, $fieldName, count($ids));

  foreach ($ids as $position => $id) {
    $entity = $storage->load($id);
    if (!$entity instanceof ContentEntityInterface) {
      continue;
    }
    $changed = FALSE;
    $entityFiles = [];
    $entityError = NULL;
    $rows = [];

    foreach (array_keys($entity->getTranslationLanguages()) as $langcode) {
      $translation = $entity->getTranslation($langcode);
      $item = $translation->get($fieldName);
      $html = $item->value ?? '';
      if (!str_contains($html, 'data:image/')) {
        continue;
      }
      $stats['translations']++;

      $doc = Html::load($html);
      $signature = b64_text_signature($doc);
      $count = 0;
      $paths = [];
      $errors = [];

      foreach (iterator_to_array($doc->getElementsByTagName('img')) as $index => $img) {
        $src = trim($img->getAttribute('src'));
        if (!preg_match('#^data:image/([a-z0-9.+-]+);base64,#i', $src, $m)) {
          continue;
        }
        $label = "img#$index";
        try {
          $type = strtolower($m[1]);
          $ext = B64_EXTENSIONS[$type] ?? throw new \RuntimeException("unsupported type image/$type");
          $binary = base64_decode(preg_replace('/\s+/', '', substr($src, strlen($m[0]))), TRUE);
          if ($binary === FALSE || $binary === '') {
            throw new \RuntimeException('invalid base64');
          }
          if ($ext !== 'svg' && !@getimagesizefromstring($binary)) {
            throw new \RuntimeException('not a decodable image');
          }

          $filename = sha1($binary) . '.' . $ext;
          $file = $seen[$filename] ?? NULL;
          if (!$file && ($file = b64_existing_file($filename))) {
            $seen[$filename] = $file;
            $stats['files_reused']++;
          }

          if (!$file) {
            $uri = B64_BASE_DIR . '/' . date('Y-m') . '/' . $filename;
            [$width, $height] = $ext === 'svg' ? [NULL, NULL] : getimagesizefromstring($binary);
            $resize = $ext !== 'svg' && $width > B64_MAX_WIDTH;

            if ($dryRun) {
              // Unsaved stand-in: provides the URL and a UUID for the rewrite.
              $file = File::create(['uri' => $uri, 'filename' => $filename]);
              $dimensions[$filename] = $resize
                ? [B64_MAX_WIDTH, (int) round($height * B64_MAX_WIDTH / $width)]
                : [$width, $height];
              $size = strlen($binary);
            }
            else {
              $directory = dirname($uri);
              $fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
              // A file left by an interrupted run has the same content (the
              // name is its hash), so replacing it is harmless.
              $fileSystem->saveData($binary, $uri, FileExists::Replace);
              if ($resize) {
                $image = $imageFactory->get($uri);
                if (!$image->isValid() || !$image->scale(B64_MAX_WIDTH) || !$image->save()) {
                  $fileSystem->delete($uri);
                  throw new \RuntimeException("resize failed for {$width}px image");
                }
              }
              $file = File::create([
                'uri' => $uri,
                'filename' => $filename,
                'filemime' => $mimeGuesser->guessMimeType($uri),
                'uid' => $entity instanceof EntityOwnerInterface ? $entity->getOwnerId() : 1,
              ]);
              $file->setPermanent();
              $file->save();
              $size = (int) $file->getSize();
            }
            $seen[$filename] = $file;
            $stats['files_created']++;
            $stats['new_file_bytes'] += $size;
            $stats['resized'] += (int) $resize;
          }

          // Dimensions of the stored file (SVG: none).
          if (!array_key_exists($filename, $dimensions)) {
            $image = $imageFactory->get($file->getFileUri());
            $dimensions[$filename] = $image->isValid() ? [$image->getWidth(), $image->getHeight()] : [NULL, NULL];
          }
          [$width, $height] = $dimensions[$filename];

          $img->setAttribute('src', $fileUrlGenerator->generateString($file->getFileUri()));
          $img->setAttribute('data-entity-type', 'file');
          $img->setAttribute('data-entity-uuid', $file->uuid());
          if ($width && $height) {
            $img->setAttribute('width', (string) $width);
            $img->setAttribute('height', (string) $height);
          }
          $count++;
          $paths[] = $file->getFileUri();
        }
        catch (\Throwable $e) {
          $errors[] = "$label: " . $e->getMessage();
          $stats['failed_images']++;
        }
      }

      $newHtml = $html;
      if ($count) {
        $newHtml = Html::serialize($doc);
        // Guard against DOM round-trip damage to anything but images.
        if (b64_text_signature(Html::load($newHtml)) !== $signature) {
          $errors[] = 'text content changed after rewrite; translation skipped';
          $stats['failed_images'] += $count;
          $count = 0;
          $paths = [];
          $newHtml = $html;
        }
        else {
          $item->value = $newHtml;
          $changed = TRUE;
          $entityFiles = array_merge($entityFiles, $paths);
          $stats['images'] += $count;
          $stats['freed_bytes'] += strlen($html) - strlen($newHtml);
        }
      }
      $rows[] = [
        $entityTypeId,
        $id,
        $langcode,
        strlen($html),
        strlen($newHtml),
        $count,
        implode('|', array_unique($paths)),
        implode('; ', $errors),
      ];
    }

    if ($changed && !$dryRun) {
      try {
        if ($entity->getEntityType()->isRevisionable()) {
          $entity->setNewRevision(TRUE);
          if ($entity instanceof RevisionLogInterface) {
            $entity->setRevisionLogMessage(B64_REVISION_LOG);
            $entity->setRevisionCreationTime(\Drupal::time()->getRequestTime());
            $entity->setRevisionUserId(1);
          }
        }
        // Keep the "changed" timestamp: content is not edited, only storage.
        $entity->setSyncing(TRUE);
        $entity->save();

        // The editor module records usage on save; verify it.
        foreach (array_unique($entityFiles) as $uri) {
          $file = $seen[basename($uri)] ?? b64_existing_file(basename($uri));
          $usage = $file ? $fileUsage->listUsage($file) : [];
          if (empty($usage['editor'][$entityTypeId][$id])) {
            $entityError = "no editor file_usage for $uri";
            break;
          }
        }
      }
      catch (\Throwable $e) {
        $entityError = 'save failed: ' . $e->getMessage();
      }
    }

    foreach ($rows as $row) {
      if ($entityError) {
        $row[7] = trim($row[7] . '; ' . $entityError, '; ');
      }
      fputcsv($log, $row, escape: '');
    }
    $stats['entities']++;
    $stats['failed_entities'] += (int) ($entityError !== NULL);
    if ($entityError) {
      printf("  ! %s %d: %s\n", $entityTypeId, $id, $entityError);
    }
    if (($position + 1) % 25 === 0) {
      printf("  %d/%d\n", $position + 1, count($ids));
    }

    // Bodies can be megabytes each; do not keep them around.
    $storage->resetCache([$id]);
  }
}

fclose($log);

$mb = fn(int $bytes) => number_format($bytes / 1048576, 2);
printf("\nSummary%s\n", $dryRun ? ' (dry run)' : '');
printf("  Entities processed:      %d (%d translations)\n", $stats['entities'], $stats['translations']);
printf("  Images migrated:         %d\n", $stats['images']);
printf("  Files created:           %d (%d resized to %dpx; %d reused from earlier runs)\n", $stats['files_created'], $stats['resized'], B64_MAX_WIDTH, $stats['files_reused']);
printf("  Freed from field data:   %s MB\n", $mb($stats['freed_bytes']));
printf("  New files on disk:       %s MB%s\n", $mb($stats['new_file_bytes']), $dryRun ? ' (before resizing)' : '');
printf("  Failed images:           %d\n", $stats['failed_images']);
printf("  Failed entity saves:     %d\n", $stats['failed_entities']);
printf("  Log: %s\n", $logPath);
