<?php

use Drupal\node\Entity\Node;

/**
 * Export ONLY a FIXED set of hard-coded NIDs' translation (title + body) to JSONL.
 * - Any content type is allowed.
 * - Exports the specified language translation (default: en).
 */

// ===============================
// 🔒 HARD-CODE TARGET NIDS HERE
// ===============================
$TARGET_NIDS = [
  666,
  664,
  659,
  658,
  663,
  667,
  668,
  669,
  670,
  671,
];
// ===============================

$options = \Drush\Drush::input()->getOptions();

$lang   = (string) ($options['lang'] ?? 'en');
$outUri = (string) ($options['out'] ?? 'private://translate/export_nodes_en.jsonl');

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

function has_cjk(string $text): bool {
  $text = trim(strip_tags(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
  if ($text === '') return false;
  return (bool) preg_match('/[\x{4E00}-\x{9FFF}\x{3400}-\x{4DBF}\x{F900}-\x{FAFF}]/u', $text);
}

/**
 * Try to keep DB connection alive.
 */
function db_keepalive(\Psr\Log\LoggerInterface $logger): void {
  try {
    \Drupal::database()->query('SELECT 1')->fetchField();
  }
  catch (\Throwable $e) {
    $logger->warning("DB keepalive failed: " . $e->getMessage());
  }
}

// Resolve output path
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
  "Export translation for hard-coded NIDs: [" . implode(',', $TARGET_NIDS) . "], " .
  "lang={$lang}, out={$realpath}, only-cjk=" . ($onlyCjk ? '1' : '0')
);

$storage = \Drupal::entityTypeManager()->getStorage('node');

$exported = 0;
$skipped  = 0;
$failed   = 0;

foreach ($TARGET_NIDS as $nid) {
  try {
    db_keepalive($logger);

    /** @var \Drupal\node\Entity\Node|null $node */
    $node = $storage->load((int) $nid);
    if (!$node) {
      $failed++;
      $logger->warning("nid={$nid} not found");
      continue;
    }

    if (!$node->hasTranslation($lang)) {
      $skipped++;
      continue;
    }

    $t = $node->getTranslation($lang);

    $title = (string) $t->label();
    $body  = $t->hasField('body') ? (string) $t->get('body')->value : '';
    $fmt   = $t->hasField('body') ? (string) $t->get('body')->format : '';

    if ($onlyCjk && !has_cjk($title) && !has_cjk($body)) {
      $skipped++;
      continue;
    }

    $row = [
      'nid'         => (int) $nid,
      'type'        => (string) $node->bundle(),   // ✅ actual content type
      'lang'        => $lang,
      'body_format' => $fmt ?: null,
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

fclose($fp);

$logger->notice(
  "✅ Export done (hard-coded nids). exported={$exported}, skipped={$skipped}, failed={$failed}. File={$realpath}"
);
