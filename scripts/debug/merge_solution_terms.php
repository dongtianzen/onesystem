<?php

/**
 * Drush PHP script: 合并旧 solution taxonomy terms 到新 term（英文默认）
 * 直接操作数据库字段表，绕开 node->save()
 *
 * 用法:
 *   drush php:script scripts/debug/merge_solution_terms.php
 *
 * Dry run:
 *   drush php:script scripts/debug/merge_solution_terms.php -- --dry-run
 */

use Drupal\taxonomy\Entity\Term;

$vocabulary           = 'solution';
$field_table          = 'node__field_article_solution';
$field_revision_table = 'node_revision__field_article_solution';

$dry_run = in_array('--dry-run', $_SERVER['argv'] ?? []);

if ($dry_run) {
  echo "========== DRY RUN 模式（不会修改任何数据）==========\n\n";
}

$database = \Drupal::database();
try {
  $database->query("SET SESSION wait_timeout=28800");
  $database->query("SET SESSION interactive_timeout=28800");
  echo "[配置] MySQL 超时已延长至 8 小时\n\n";
} catch (\Exception $e) {
  echo "[警告] 无法设置 MySQL 超时: " . $e->getMessage() . "\n\n";
}

$merge_map = [
  [
    'en'       => 'Digital Frontend System Integration',
    'old_tids' => [190, 185],
  ],
  [
    'en'       => 'Live & Remote Production System',
    'old_tids' => [95, 188],
  ],
  [
    'en'       => 'Studio & Master Control',
    'old_tids' => [277, 96],
  ],
  [
    'en'       => 'Transmission & Distribution',
    'old_tids' => [99, 100, 187],
  ],
  [
    'en'       => 'Playout & Storage',
    'old_tids' => [98, 186, 97],
  ],
  [
    'en'       => 'Monitoring & Analytics System',
    'old_tids' => [189],
  ],
];

$term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');

foreach ($merge_map as $entry) {

  $new_term_en = $entry['en'];
  $old_tids    = $entry['old_tids'];

  // --- 1. 获取或创建新 term（英文，无中文翻译）---
  $existing_new = $term_storage->loadByProperties([
    'name' => $new_term_en,
    'vid'  => $vocabulary,
  ]);

  if ($existing_new) {
    $new_term = reset($existing_new);
    $new_tid  = $new_term->id();
    echo "[已存在] 新 term: \"{$new_term_en}\" (tid={$new_tid})\n";
  }
  elseif ($dry_run) {
    echo "[DRY RUN] 将创建新 term: \"{$new_term_en}\"\n";
    $new_tid = 0;
  }
  else {
    $new_term = Term::create([
      'name'     => $new_term_en,
      'vid'      => $vocabulary,
      'langcode' => 'en',
    ]);
    $new_term->save();
    $new_tid = $new_term->id();
    echo "[已创建] 新 term: \"{$new_term_en}\" (tid={$new_tid})\n";
  }

  // --- 2. 处理每一个旧 term ---
  foreach ($old_tids as $old_tid) {

    $old_term = $term_storage->load($old_tid);

    if (!$old_term) {
      echo "  [已跳过] tid={$old_tid} 不存在（可能已处理过）\n";
      continue;
    }

    $old_label = $old_term->label();
    echo "  [处理] tid={$old_tid} \"{$old_label}\" → \"{$new_term_en}\" (tid={$new_tid})\n";

    if ($dry_run) {
      $count = $database->select($field_table, 'f')
        ->condition('f.field_article_solution_target_id', $old_tid)
        ->condition('f.bundle', 'article')
        ->countQuery()
        ->execute()
        ->fetchField();
      echo "    [DRY RUN] 将更新 {$count} 条记录\n";
      echo "    [DRY RUN] 将删除 tid={$old_tid} \"{$old_label}\"\n";
    }
    else {
      $count = $database->update($field_table)
        ->fields(['field_article_solution_target_id' => $new_tid])
        ->condition('field_article_solution_target_id', $old_tid)
        ->condition('bundle', 'article')
        ->execute();
      echo "    [更新] 主表 {$count} 条记录\n";

      if ($database->schema()->tableExists($field_revision_table)) {
        $rcount = $database->update($field_revision_table)
          ->fields(['field_article_solution_target_id' => $new_tid])
          ->condition('field_article_solution_target_id', $old_tid)
          ->condition('bundle', 'article')
          ->execute();
        echo "    [更新] revision表 {$rcount} 条记录\n";
      }

      $old_term->delete();
      echo "    [已删除] tid={$old_tid} \"{$old_label}\"\n";
    }
  }

  echo "\n";
}

echo "==============================\n";
echo $dry_run ? "DRY RUN 完成！确认无误后去掉 --dry-run 正式运行。\n" : "全部完成！请手动执行: drush cr\n";
echo "==============================\n";
