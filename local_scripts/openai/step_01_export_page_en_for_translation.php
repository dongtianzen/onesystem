<?php

/**
 * Step 1: Export ONLY article content type's EN translation (title + body) to JSONL.
 * - Your site has both zh-hans and en translations already.
 * - Currently en content is still Chinese; we export en (as-is) and translate it locally later.
 * - Export format: JSONL (one JSON per line).
 * - Prints each exported nid (one per line).
 *
 * Usage:
 *  ddev drush scr local_scripts/openai/step_01_export_article_en_for_translation.php \
 *    --lang=en --out=private://translate/export_article_en.jsonl --only-cjk=1
 *
 * Optional:
 *  --limit=10
 *  --start-nid=1200
 *  --only-cjk=1|0   (recommended) only export if EN title/body contains any CJK char
 */

use Drupal\node\Entity\Node;

$options = \Drush\Drush::input()->getOptions();

$lang     = (string) ($options['lang'] ?? 'en');
$outUri   = (string) ($options['out'] ?? 'private://translate/export_article_en.jsonl');
$limit    = (int) ($options['limit'] ?? 0);
$startNid = (int) ($options['start-nid'] ?? 0);

// Correct boolean parsing: --only-cjk=0 should be FALSE.
$onlyCjkRaw = $options['only-cjk'] ?? '1';
$onlyCjk = filter_var($onlyCjkRaw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
if ($onlyCjk === null) {
  $onlyCjk = true;
}

$logger = \Drush\Drush::logger();

if ($lang === '') {
  throw new \Exception("Missing --lang");
}

// ✅ Hard-coded: ONLY export article content type
$types = ['article'];

function has_cjk(string $text): bool {
  $text = trim(strip_tags(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
  if ($text === '') return false;
  return (bool) preg_match('/[\x{4E00}-\x{9FFF}\x{3400}-\x{4DBF}\x{F900}-\x{FAFF}]/u', $text);
}

/**
 * Try to keep DB connection alive.
 * If "server has gone away", try to re-acquire connection once.
 */
function db_keepalive(\Psr\Log\LoggerInterface $logger): void {
  try {
    \Drupal::database()->query('SELECT 1')->fetchField();
  }
  catch (\Throwable $e) {
    $msg = $e->getMessage();
    $logger->warning("DB keepalive failed: {$msg}");

    // If connection dropped, re-acquire and try once more.
    if (stripos($msg, 'server has gone away') !== false || stripos($msg, '2006') !== false) {
      try {
        // Re-acquire default connection.
        $db = \Drupal::database();
        $db->query('SELECT 1')->fetchField();
        $logger->notice("DB keepalive retry succeeded after reconnect.");
      }
      catch (\Throwable $e2) {
        $logger->warning("DB keepalive retry failed: " . $e2->getMessage());
      }
    }
  }
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
  "Export EN for translation (ARTICLE ONLY): lang={$lang}, type=article, out={$realpath}, " .
  "only-cjk=" . ($onlyCjk ? '1' : '0') .
  ", start-nid>{$startNid}, limit=" . ($limit ?: '0(unlimited)')
);

$storage  = \Drupal::entityTypeManager()->getStorage('node');

$exported = 0;
$skipped  = 0;
$failed   = 0;

// Paging config: smaller page => less memory & less chance of long idle time.
// You can tune these two numbers if needed.
$pageSize = 100;   // how many nids to query per page
$loadBatch = 30;   // how many to loadMultiple at once inside a page

$offset = 0;

while (true) {
  // Respect --limit (counts all processed: exported+skipped+failed)
  if ($limit > 0) {
    $processed = $exported + $skipped + $failed;
    $remaining = $limit - $processed;
    if ($remaining <= 0) {
      break;
    }
    $range = min($pageSize, $remaining);
  }
  else {
    $range = $pageSize;
  }

  // Query a page of nids (no giant array)
  $query = \Drupal::entityQuery('node')
    ->accessCheck(FALSE)
    ->condition('type', $types, 'IN')
    ->sort('nid', 'ASC')
    ->range($offset, $range);

  if ($startNid > 0) {
    $query->condition('nid', $startNid, '>');
  }

  $nids = array_values($query->execute());
  if (!$nids) {
    break; // done
  }

  // Keep DB alive once per page
  db_keepalive($logger);

  // Load in smaller chunks to reduce memory footprint
  foreach (array_chunk($nids, $loadBatch) as $chunk) {
    // Another keepalive before loadMultiple can help on slow sites
    db_keepalive($logger);

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

        $title = (string) $t->label();
        $body  = $t->hasField('body') ? (string) $t->get('body')->value : '';
        $fmt   = $t->hasField('body') ? (string) $t->get('body')->format : 'basic_html';

        if ($onlyCjk) {
          if (!has_cjk($title) && !has_cjk($body)) {
            $skipped++;
            continue;
          }
        }

        $row = [
          'nid'         => (int) $nid,
          'type'        => 'article',
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

        // If DB dropped mid-loop, try keepalive so subsequent items might continue
        if (stripos($e->getMessage(), 'server has gone away') !== false || stripos($e->getMessage(), '2006') !== false) {
          db_keepalive($logger);
        }
      }
    }

    // Free memory aggressively
    unset($nodes);
  }

  $offset += $range;
}

fclose($fp);

$logger->notice("✅ Export done. exported={$exported}, skipped={$skipped}, failed={$failed}. File={$realpath}");
