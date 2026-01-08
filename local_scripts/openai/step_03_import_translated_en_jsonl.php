<?php

use Drupal\node\Entity\Node;

/**
 * 导入脚本（写回 en translation）
 *
 * 导入：把 translated_en.jsonl 写回到 en translation（覆盖 title/body）
 *
 * 用法：
 *  ddev drush scr local_scripts/openai/step_03_import_translated_en_jsonl.php \
 *    --in=private://translate/translated_en.jsonl --lang=en
 *
 * Dry run：
 *  --dry-run=1
 */

$options = \Drush\Drush::input()->getOptions();

$inUri   = (string)($options['in'] ?? 'private://translate/translated_page_en.jsonl');
$lang    = (string)($options['lang'] ?? 'en');
$dryRun  = (bool)($options['dry-run'] ?? false);

$logger = \Drush\Drush::logger();

$realpath = \Drupal::service('file_system')->realpath($inUri) ?: $inUri;
if (!is_file($realpath)) {
  throw new \Exception("Input file not found: {$realpath}");
}

$fp = fopen($realpath, 'r');
if (!$fp) throw new \Exception("Cannot open input file: {$realpath}");

$storage = \Drupal::entityTypeManager()->getStorage('node');

$updated = 0;
$skipped = 0;
$failed  = 0;

while (($line = fgets($fp)) !== false) {
  $line = trim($line);
  if ($line === '') continue;

  try {
    $row = json_decode($line, true);
    if (!is_array($row)) { $failed++; continue; }

    $nid = (int)($row['nid'] ?? 0);
    if (!$nid) { $failed++; continue; }

    $titleNew = (string)($row['title_new'] ?? '');
    $bodyNew  = (string)($row['body_new'] ?? '');
    $fmt      = (string)($row['body_format'] ?? 'basic_html');
    $rowLang  = (string)($row['lang'] ?? $lang);

    if ($rowLang !== $lang) {
      $skipped++;
      continue;
    }

    /** @var \Drupal\node\Entity\Node|null $node */
    $node = $storage->load($nid);
    if (!$node) { $failed++; continue; }

    if (!$node->hasTranslation($lang)) {
      // 你说都有 en；这里为了安全，如果没有就跳过
      $failed++;
      $logger->error("nid={$nid} missing translation {$lang}");
      continue;
    }

    $t = $node->getTranslation($lang);

    // 简单兜底：title 不能空
    if (trim($titleNew) === '') {
      $titleNew = (string)$t->label();
      if (trim($titleNew) === '') $titleNew = "Untitled";
    }

    if ($dryRun) {
      $logger->notice("DRY-RUN would update nid={$nid} lang={$lang}");
      print $nid . PHP_EOL;
      continue;
    }

    $t->setTitle($titleNew);

    if ($t->hasField('body')) {
      $t->set('body', [
        'value'  => $bodyNew,
        'format' => $fmt,
      ]);
    }

    $node->save();
    $updated++;

    // 每行打印一个 nid（你要的）
    print $nid . PHP_EOL;

  } catch (\Throwable $e) {
    $failed++;
    $logger->error("Import failed: " . $e->getMessage());
  }
}

fclose($fp);

$logger->notice("✅ Import done. updated={$updated}, skipped={$skipped}, failed={$failed}");
