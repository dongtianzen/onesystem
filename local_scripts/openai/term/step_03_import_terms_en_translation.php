<?php

use Drupal\taxonomy\Entity\Term;

/**
 * Step 3: Import translated EN fields into taxonomy term translations.
 *
 * Run:
 *  ddev drush scr local_scripts/openai/term/step_03_import_terms_en_translation.php
 */

$IN_FILE   = 'private://translate/terms_translated_en.jsonl';
$DST_LANG  = 'en';

$BATCH_SAVE = 50;     // 每多少个 save 一次（主要是节奏控制）
$DRY_RUN    = 0;      // 1=只打印不写入

function norm_text($s): string {
  $s = (string) $s;
  $s = str_replace(["\r\n", "\r"], "\n", $s);
  return trim($s);
}

$realIn = \Drupal::service('file_system')->realpath($IN_FILE);
if (!$realIn || !file_exists($realIn)) {
  fwrite(STDERR, "Cannot read IN_FILE: $IN_FILE\n");
  exit(1);
}

$fh = fopen($realIn, 'rb');
if (!$fh) {
  fwrite(STDERR, "Cannot open: $realIn\n");
  exit(1);
}

$storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');

$count = 0;
$updated = 0;
$lineNo = 0;

while (($line = fgets($fh)) !== false) {
  $lineNo++;
  $line = trim($line);
  if ($line === '') continue;

  $row = json_decode($line, true);
  if (!is_array($row)) {
    fwrite(STDERR, "Skip invalid JSON at line $lineNo\n");
    continue;
  }

  $tid = (int)($row['tid'] ?? 0);
  if ($tid <= 0) continue;

  /** @var \Drupal\taxonomy\Entity\Term|null $term */
  $term = $storage->load($tid);
  if (!$term) {
    fwrite(STDERR, "Term not found tid=$tid at line $lineNo\n");
    continue;
  }

  $nameTr = norm_text((string)($row['name_translated'] ?? ''));
  $descTr = norm_text((string)(($row['description_translated']['value'] ?? '')));
  $descFormat = (string)(($row['description_translated']['format'] ?? 'basic_html'));

  // Create or get EN translation
  $tr = $term->hasTranslation($DST_LANG) ? $term->getTranslation($DST_LANG) : $term->addTranslation($DST_LANG);

  $changed = false;

  if ($nameTr !== '') {
    $old = norm_text($tr->label());
    if ($old !== $nameTr) {
      $tr->setName($nameTr);
      $changed = true;
    }
  }

  if ($descTr !== '' && $tr->hasField('description')) {
    $oldDesc = '';
    if (!$tr->get('description')->isEmpty()) {
      $oldDesc = norm_text((string)($tr->get('description')->value ?? ''));
    }
    if ($oldDesc !== $descTr) {
      $tr->set('description', [
        'value' => $descTr,
        'format' => $descFormat ?: 'basic_html',
      ]);
      $changed = true;
    }
  }

  if ($changed) {
    $updated++;
    if ($DRY_RUN) {
      echo "DRY_RUN tid=$tid updated\n";
    } else {
      $term->save();
      echo "tid=$tid saved\n";
    }
  }

  $count++;
  if ($count % $BATCH_SAVE === 0) {
    // Keep DB connection alive.
    try { \Drupal::database()->query('SELECT 1'); } catch (\Throwable $e) {}
  }
}

fclose($fh);

fwrite(STDERR, "Done. Lines=$lineNo, Processed=$count, Updated=$updated, DryRun=$DRY_RUN\n");
