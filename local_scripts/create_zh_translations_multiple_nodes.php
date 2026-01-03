<?php

use Drupal\node\Entity\Node;

/**
 * ddev drush scr local_scripts/create_zh_translations_multiple_nodes.php
 * 先 dry-run 看看会处理哪些
 * ddev drush scr scripts/create_zh_translations.php --dry-run
 */

/**
 *
 * Drush script: Create zh-hans translations for nodes of certain content types.
 *
 * Usage examples:
 *   ddev drush scr scripts/create_zh_translations.php
 *   ddev drush scr scripts/create_zh_translations.php --lang=zh-hans
 *   ddev drush scr scripts/create_zh_translations.php --types=article,page
 *   ddev drush scr scripts/create_zh_translations.php --limit=200 --offset=0
 *   ddev drush scr scripts/create_zh_translations.php --dry-run
 */

// -------------------- Parse CLI options --------------------
$args = $_SERVER['argv'] ?? [];
$options = [
  'lang' => 'zh-hans',
  'types' => 'article,page',
  'limit' => 0,   // 0 means no limit
  'offset' => 0,
  'dry-run' => false,
];

foreach ($args as $arg) {
  if (preg_match('/^--lang=(.+)$/', $arg, $m)) {
    $options['lang'] = trim($m[1]);
  }
  elseif (preg_match('/^--types=(.+)$/', $arg, $m)) {
    $options['types'] = trim($m[1]);
  }
  elseif (preg_match('/^--limit=(\d+)$/', $arg, $m)) {
    $options['limit'] = (int) $m[1];
  }
  elseif (preg_match('/^--offset=(\d+)$/', $arg, $m)) {
    $options['offset'] = (int) $m[1];
  }
  elseif ($arg === '--dry-run') {
    $options['dry-run'] = true;
  }
}

$lang = $options['lang'];
$types = array_values(array_filter(array_map('trim', explode(',', $options['types']))));
$limit = (int) $options['limit'];
$offset = (int) $options['offset'];
$dry_run = (bool) $options['dry-run'];

if (empty($types)) {
  throw new \Exception('No content types provided. Use --types=article,page');
}

print "=== Create translations ===\n";
print "lang: {$lang}\n";
print "types: " . implode(', ', $types) . "\n";
print "limit: {$limit}\n";
print "offset: {$offset}\n";
print "dry-run: " . ($dry_run ? 'YES' : 'NO') . "\n\n";

// -------------------- Query nodes --------------------
$query = \Drupal::entityQuery('node')
  ->condition('type', $types, 'IN')
  ->accessCheck(FALSE)
  ->sort('nid', 'ASC');

if ($limit > 0) {
  $query->range($offset, $limit);
}

$nids = $query->execute();
$total = count($nids);

print "Found {$total} nodes in this batch.\n\n";

$created = 0;
$skipped = 0;
$errors = 0;

foreach ($nids as $nid) {
  try {
    $node = Node::load($nid);
    if (!$node) {
      $errors++;
      print "[ERROR] Node {$nid} not found.\n";
      continue;
    }

    if ($node->hasTranslation($lang)) {
      $skipped++;
      // print "[SKIP] {$nid} already has {$lang}\n";
      continue;
    }

    if ($dry_run) {
      $created++;
      print "[DRY] Would create {$lang} for node {$nid}\n";
      continue;
    }

    // Create translation using current node values as baseline.
    $translation = $node->addTranslation($lang, $node->toArray());

    // Inherit published status.
    $translation->setPublished($node->isPublished());

    // Inherit moderation_state if present.
    if ($translation->hasField('moderation_state') && $node->hasField('moderation_state')) {
      $translation->set('moderation_state', $node->get('moderation_state')->value);
    }

    $node->save();
    $created++;
    print "[OK] Created {$lang} for node {$nid}\n";
  }
  catch (\Throwable $e) {
    $errors++;
    print "[ERROR] nid={$nid} " . $e->getMessage() . "\n";
  }
}

print "\n=== Done ===\n";
print "Created: {$created}\n";
print "Skipped (already existed): {$skipped}\n";
print "Errors: {$errors}\n";
