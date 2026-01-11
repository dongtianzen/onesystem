<?php

use Drupal\taxonomy\Entity\Term;

/**
 * Step 1: Export taxonomy terms (ZH) to JSONL for translation.
 *
 * - Export fields: tid, vid, src_lang, dst_lang, name, description(value/format)
 * - Supports paging + low memory
 *
 * Run:
 *  ddev drush scr local_scripts/openai/term/step_01_export_terms_zh_for_translation.php
 */

$OUT_FILE   = 'private://translate/terms_export_zh.jsonl';
$SRC_LANG   = 'zh-hans';
$DST_LANG   = 'en';

$VOCABS     = [];        // e.g. ['product_category', 'tags']; empty = all vocabs
$BATCH_SIZE = 200;       // paging size
$ONLY_CJK   = 1;         // only export if name/desc contains CJK
$MIN_CHARS  = 2;         // minimum chars to export per field

// -------- helpers --------
function contains_cjk($s): bool {
  if ($s === null) return false;
  return (bool) preg_match('/[\x{4E00}-\x{9FFF}\x{3400}-\x{4DBF}\x{3040}-\x{30FF}\x{AC00}-\x{D7AF}]/u', $s);
}

function norm_text($s): string {
  $s = (string) $s;
  $s = str_replace(["\r\n", "\r"], "\n", $s);
  return trim($s);
}

function should_export_field(string $text, int $minChars, int $onlyCjk): bool {
  $text = norm_text($text);
  if (mb_strlen($text) < $minChars) return false;
  if ($onlyCjk && !contains_cjk($text)) return false;
  return true;
}

// -------- main --------
$realOut = \Drupal::service('file_system')->realpath($OUT_FILE);
if (!$realOut) {
  // Ensure directory exists.
  $dir = dirname($OUT_FILE);
  \Drupal::service('file_system')->prepareDirectory($dir, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY | \Drupal\Core\File\FileSystemInterface::MODIFY_PERMISSIONS);
  $realOut = \Drupal::service('file_system')->realpath($OUT_FILE);
}
if (!$realOut) {
  fwrite(STDERR, "Cannot resolve OUT_FILE: $OUT_FILE\n");
  exit(1);
}

$fh = fopen($realOut, 'wb');
if (!$fh) {
  fwrite(STDERR, "Cannot open for write: $realOut\n");
  exit(1);
}

fwrite(STDERR, "Exporting terms to: $realOut\n");

$storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');

$offset = 0;
$totalWritten = 0;

while (true) {
  $query = $storage->getQuery()
    ->accessCheck(FALSE)
    ->sort('tid', 'ASC')
    ->range($offset, $BATCH_SIZE);

  if (!empty($VOCABS)) {
    $query->condition('vid', $VOCABS, 'IN');
  }

  $tids = $query->execute();
  if (empty($tids)) break;

  /** @var \Drupal\taxonomy\Entity\Term[] $terms */
  $terms = $storage->loadMultiple($tids);

  foreach ($terms as $term) {
    $vid = $term->bundle();
    $tid = (int) $term->id();

    // Try to use SRC_LANG translation if exists; else fallback to default.
    $src = $term->hasTranslation($SRC_LANG) ? $term->getTranslation($SRC_LANG) : $term;

    $name = norm_text($src->label());

    $descValue = '';
    $descFormat = 'basic_html';
    if ($src->hasField('description') && !$src->get('description')->isEmpty()) {
      $descValue = norm_text($src->get('description')->value ?? '');
      $descFormat = (string) ($src->get('description')->format ?? 'basic_html');
    }

    $exportName = should_export_field($name, $MIN_CHARS, $ONLY_CJK);
    $exportDesc = should_export_field($descValue, $MIN_CHARS, $ONLY_CJK);

    if (!$exportName && !$exportDesc) {
      continue;
    }

    $row = [
      'entity_type' => 'taxonomy_term',
      'tid' => $tid,
      'vid' => $vid,
      'src_lang' => $SRC_LANG,
      'dst_lang' => $DST_LANG,
      'name' => $exportName ? $name : '',
      'description' => [
        'value' => $exportDesc ? $descValue : '',
        'format' => $descFormat,
      ],
    ];

    fwrite($fh, json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
    $totalWritten++;
    echo $tid . "\n";
  }

  $offset += $BATCH_SIZE;

  // Keep DB connection alive in long runs.
  try { \Drupal::database()->query('SELECT 1'); } catch (\Throwable $e) {}
}

fclose($fh);

fwrite(STDERR, "Done. Exported lines: $totalWritten\n");
