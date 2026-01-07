<?php

use Drupal\node\Entity\Node;

/**
 * Step 1: Export ONLY page content type's EN translation (title + body) to JSONL.
 * - Your site has both zh-hans and en translations already.
 * - Currently en content is still Chinese; we export en (as-is) and translate it locally later.
 * - Export format: JSONL (one JSON per line).
 * - Prints each exported nid (one per line).
 *
 * Usage:
 *  ddev drush scr local_scripts/step_01_export_page_en_for_translation.php \
 *    --lang=en --out=private://translate/export_page_en.jsonl --only-cjk=1
 *
 * Optional:
 *  --limit=10
 *  --start-nid=1200
 *  --only-cjk=1   (recommended) only export if EN title/body contains any CJK char
 */

$options = \Drush\Drush::input()->getOptions();

$lang     = (string)($options['lang'] ?? 'en');
$outUri   = (string)($options['out'] ?? 'private://translate/export_page_en.jsonl');
$limit    = (int)($options['limit'] ?? 0);
$startNid = (int)($options['start-nid'] ?? 0);
$onlyCjk  = (bool)($options['only-cjk'] ?? true);

$logger = \Drush\Drush::logger();

if ($lang === '') {
  throw new \Exception("Missing --lang");
}

// ✅ Hard-coded: ONLY export page content type
$types = ['page'];

function has_cjk(string $text): bool {
  $text = trim(strip_tags(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
  if ($text === '') return false;
  return (bool)preg_match('/[\x{4E00}-\x{9FFF}\x{3400}-\x{4DBF}\x{F900}-\x{FAFF}]/u', $text);
}

// Resolve private:// to real path (fallback to raw path if not a stream wrapper)
$realpath = \Drupal::service('file_system')->realpath($outUri) ?: $outUri;

// Ensure directory exists
$dir = dirname($realpath);
if (!is_dir($dir)) {
  if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
    throw new \Exception("Cannot create directory: $dir");
  }
}

$fp = fopen($realpath, 'w');
if (!$fp) {
  throw new \Exception("Cannot open output file: $realpath");
}

$logger->notice(
  "Export EN for translation (PAGE ONLY): lang={$lang}, type=page, out={$realpath}, " .
  "only-cjk=" . ($onlyCjk ? '1' : '0') .
  ", start-nid>{$startNid}, limit=" . ($limit ?: '0(unlimited)')
);

// Query ONLY page nodes
$query = \Drupal::entityQuery('node')
  ->accessCheck(FALSE)
  ->condition('type', $types, 'IN')
  ->sort('nid', 'ASC');

if ($startNid > 0) {
  $query->condition('nid', $startNid, '>');
}
if ($limit > 0) {
  $query->range(0, $limit);
}

$nids  = array_values($query->execute());
$total = count($nids);

$logger->notice("Found {$total} candidate page nodes.");

$storage  = \Drupal::entityTypeManager()->getStorage('node');
$exported = 0;
$skipped  = 0;
$failed   = 0;

foreach (array_chunk($nids, 50) as $chunk) {
  $nodes = $storage->loadMultiple($chunk);

  foreach ($chunk as $nid) {
    try {
      /** @var \Drupal\node\Entity\Node|null $node */
      $node = $nodes[$nid] ?? null;
      if (!$node) {
        $failed++;
        $logger->warning("nid={$nid} not loaded");
        continue;
      }

      if (!$node->hasTranslation($lang)) {
        $skipped++;
        continue;
      }

      $t = $node->getTranslation($lang);

      $title = (string)$t->label();
      $body  = $t->hasField('body') ? (string)$t->get('body')->value : '';
      $fmt   = $t->hasField('body') ? (string)$t->get('body')->format : 'basic_html';

      if ($onlyCjk) {
        // Only export if EN still contains Chinese (title or body has any CJK)
        if (!has_cjk($title) && !has_cjk($body)) {
          $skipped++;
          continue;
        }
      }

      $row = [
        'nid'         => (int)$nid,
        'type'        => 'page',
        'lang'        => $lang,
        'body_format' => $fmt,
        'title_src'   => $title,
        'body_src'    => $body,
      ];

      fwrite($fp, json_encode($row, JSON_UNESCAPED_UNICODE) . "\n");
      $exported++;

      // Print one nid per line
      print $nid . PHP_EOL;
    }
    catch (\Throwable $e) {
      $failed++;
      $logger->error("nid={$nid} export failed: " . $e->getMessage());
    }
  }
}

fclose($fp);

$logger->notice("✅ Export done. exported={$exported}, skipped={$skipped}, failed={$failed}. File={$realpath}");
