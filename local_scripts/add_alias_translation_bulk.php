<?php

/**
 * Bulk copy path aliases from one langcode to another.
 * 批量脚本：复制所有 en alias → zh-hans
 *
 * Usage:
 *   ddev drush scr local_scripts/add_alias_translation_bulk.php -- --source=en --target=zh-hans --dry-run
 *   ddev drush scr local_scripts/add_alias_translation_bulk.php -- --source=en --target=zh-hans --limit=10 --dry-run
 *   ddev drush scr local_scripts/add_alias_translation_bulk.php -- --source=en --target=zh-hans
 */

use Drupal\path_alias\Entity\PathAlias;

$options = getopt('', [
  'source::',
  'target::',
  'limit::',
  'dry-run',
]);

$source = $options['source'] ?? 'en';
$target = $options['target'] ?? 'zh-hans';
$limit  = isset($options['limit']) ? (int) $options['limit'] : 0;
$dryRun = array_key_exists('dry-run', $options);

$storage = \Drupal::entityTypeManager()->getStorage('path_alias');
$database = \Drupal::database();

$query = $database->select('path_alias', 'pa');
$query->fields('pa', ['id']);
$query->condition('pa.langcode', $source);
$query->condition('pa.path', '/node/%', 'LIKE');

if ($limit > 0) {
  $query->range(0, $limit);
}

$ids = $query->execute()->fetchCol();

$total = count($ids);
if ($total === 0) {
  print "No source aliases found for langcode={$source}\n";
  exit(0);
}

print "Found {$total} aliases in {$source}\n";

$created = 0;
$skipped = 0;

foreach ($ids as $id) {
  /** @var \Drupal\path_alias\PathAliasInterface $src */
  $src = $storage->load($id);
  if (!$src) {
    continue;
  }

  $path  = $src->getPath();
  $alias = $src->getAlias();

  // 目标是否已存在
  $exists = $storage->loadByProperties([
    'path' => $path,
    'alias' => $alias,
    'langcode' => $target,
  ]);

  if (!empty($exists)) {
    $skipped++;
    continue;
  }

  if ($dryRun) {
    print "[DRY-RUN] {$alias} ({$path}) → {$target}\n";
    continue;
  }

  $new = PathAlias::create([
    'path' => $path,
    'alias' => $alias,
    'langcode' => $target,
    'status' => 1,
  ]);
  $new->save();
  $created++;

  print "Inserted: {$alias} → {$target}\n";
}

print "\nSummary:\n";
print "  Created: {$created}\n";
print "  Skipped (already exists): {$skipped}\n";
print "  Total processed: {$total}\n";
print "  limit number is: {$limit}\n";
