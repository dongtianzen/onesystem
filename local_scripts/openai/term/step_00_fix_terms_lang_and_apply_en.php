<?php

use Drupal\taxonomy\Entity\Term;
use Drupal\Core\File\FileSystemInterface;

/**
 * Fix terms whose original language is EN but content is Chinese:
 * - Copy current EN content (Chinese) into zh-hans translation.
 * - Replace EN content with translated English from terms_translated_en.jsonl.
 *
 * This is SAFE because it does not delete anything; it only adds zh-hans translation and updates en.
 *
 * Run:
 *   ddev drush scr local_scripts/openai/term/step_00_fix_terms_lang_and_apply_en.php
 *
 * Optional: set $DRY_RUN = 1 to preview.
 */

// ====== CONFIG ======
$DST_LANG_ZH = 'zh-hans';
$ORIG_LANG_EN = 'en';

// ✅ 只填“有问题的 vocabulary”
$TARGET_VOCABS = [
  'feature_details',
];

$JSONL_FILE = 'private://translate/terms_translated_en.jsonl';

// 1 = 只打印不写入
$DRY_RUN = 0;

// 如果 term 已经有 zh-hans 翻译，是否仍覆盖 zh-hans（一般不建议覆盖）
$OVERWRITE_ZH_IF_EXISTS = 0;

// 如果 term 的 en 已经是英文（不含中文），是否仍覆盖 en（一般不建议覆盖）
$OVERWRITE_EN_IF_NO_CJK = 0;
// ====================

function contains_cjk(?string $s): bool {
  if ($s === null) return false;
  return (bool) preg_match('/[\x{4E00}-\x{9FFF}\x{3400}-\x{4DBF}\x{3040}-\x{30FF}\x{AC00}-\x{D7AF}]/u', $s);
}

function norm_text($s): string {
  $s = (string) $s;
  $s = str_replace(["\r\n", "\r"], "\n", $s);
  return trim($s);
}

function load_jsonl_map(string $jsonlPath): array {
  $map = [];
  $fh = fopen($jsonlPath, 'rb');
  if (!$fh) {
    throw new RuntimeException("Cannot open JSONL: $jsonlPath");
  }
  $lineNo = 0;
  while (($line = fgets($fh)) !== false) {
    $lineNo++;
    $line = trim($line);
    if ($line === '') continue;
    $row = json_decode($line, true);
    if (!is_array($row)) continue;

    $tid = (int)($row['tid'] ?? 0);
    if ($tid <= 0) continue;

    $map[$tid] = [
      'name_en' => norm_text((string)($row['name_translated'] ?? '')),
      'desc_en' => norm_text((string)(($row['description_translated']['value'] ?? ''))),
      'desc_format' => (string)(($row['description_translated']['format'] ?? 'basic_html')),
      'vid' => (string)($row['vid'] ?? ''),
    ];
  }
  fclose($fh);
  return $map;
}

$fs = \Drupal::service('file_system');
$realJsonl = $fs->realpath($JSONL_FILE);
if (!$realJsonl || !file_exists($realJsonl)) {
  fwrite(STDERR, "Cannot read JSONL: $JSONL_FILE\n");
  exit(1);
}

$enMap = load_jsonl_map($realJsonl);
fwrite(STDERR, "Loaded translations: " . count($enMap) . " terms\n");

$storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');

// Query terms in target vocabs
$query = $storage->getQuery()->accessCheck(FALSE);
if (!empty($TARGET_VOCABS)) {
  $query->condition('vid', $TARGET_VOCABS, 'IN');
}
$tids = $query->execute();
fwrite(STDERR, "Scanning terms: " . count($tids) . "\n");

$fixed = 0;
$skipped = 0;

foreach ($tids as $tid) {
  /** @var \Drupal\taxonomy\Entity\Term $term */
  $term = $storage->load($tid);
  if (!$term) { $skipped++; continue; }

  $vid = $term->bundle();

  // We only fix those whose original language is EN but label contains CJK (means Chinese content stored as EN).
  $origLang = $term->language()->getId(); // original language of the entity
  $enLabelNow = norm_text($term->label());

  if ($origLang !== $ORIG_LANG_EN) {
    $skipped++; continue;
  }
  if (!contains_cjk($enLabelNow)) {
    // original is en and looks already English
    $skipped++; continue;
  }

  // Get current EN fields (Chinese content)
  $enDescNow = '';
  $enDescFmtNow = 'basic_html';
  if ($term->hasField('description') && !$term->get('description')->isEmpty()) {
    $enDescNow = norm_text((string)($term->get('description')->value ?? ''));
    $enDescFmtNow = (string)($term->get('description')->format ?? 'basic_html');
  }

  // Prepare zh-hans translation from current EN content
  $zhExists = $term->hasTranslation($DST_LANG_ZH);
  if ($zhExists && !$OVERWRITE_ZH_IF_EXISTS) {
    // We still might want to fix EN, but not overwrite zh if already present.
  }

  // EN replacement from JSONL
  $enNew = $enMap[$tid]['name_en'] ?? '';
  $descNew = $enMap[$tid]['desc_en'] ?? '';
  $descFmtNew = $enMap[$tid]['desc_format'] ?? $enDescFmtNow;

  if ($enNew === '' && $descNew === '') {
    // No translated EN found for this tid -> skip to avoid wiping
    fwrite(STDERR, "Skip tid=$tid (vid=$vid): no EN translation in JSONL\n");
    $skipped++; continue;
  }

  // Decide whether to overwrite EN if it already looks English
  if (!$OVERWRITE_EN_IF_NO_CJK && !contains_cjk($enLabelNow) && $enLabelNow !== '') {
    $skipped++; continue;
  }

  if ($DRY_RUN) {
    echo "DRY_RUN tid=$tid vid=$vid\n";
    echo "  orig_lang=en (but content is Chinese)\n";
    echo "  EN(before): $enLabelNow\n";
    echo "  EN(after) : $enNew\n";
    echo "  ZH(ensure): " . ($enLabelNow ?: '[empty]') . "\n";
    continue;
  }

  // 1) Ensure zh-hans translation exists with Chinese content copied from current EN
  if (!$zhExists) {
    $zh = $term->addTranslation($DST_LANG_ZH);
  } else {
    $zh = $term->getTranslation($DST_LANG_ZH);
  }

  if (!$zhExists || $OVERWRITE_ZH_IF_EXISTS) {
    if ($enLabelNow !== '') {
      $zh->setName($enLabelNow);
    }
    if ($zh->hasField('description')) {
      $zh->set('description', [
        'value' => $enDescNow,
        'format' => $enDescFmtNow ?: 'basic_html',
      ]);
    }
  }

  // 2) Update EN original fields with real English translation
  if ($enNew !== '') {
    $term->setName($enNew);
  }
  if ($term->hasField('description')) {
    $term->set('description', [
      'value' => $descNew,
      'format' => $descFmtNew ?: 'basic_html',
    ]);
  }

  // 3) Save
  $term->save();
  $fixed++;

  echo "Fixed tid=$tid vid=$vid\n";
}

fwrite(STDERR, "Done. Fixed=$fixed Skipped=$skipped\n");
