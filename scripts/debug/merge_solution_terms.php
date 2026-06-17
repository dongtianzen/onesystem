<?php

/**
 * Drush PHP script: 合并旧 solution taxonomy terms 到新 term（双语，英文为默认）
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

// 延长 MySQL 连接超时
$database = \Drupal::database();
try {
  $database->query("SET SESSION wait_timeout=28800");
  $database->query("SET SESSION interactive_timeout=28800");
  echo "[配置] MySQL 超时已延长至 8 小时\n\n";
} catch (\Exception $e) {
  echo "[警告] 无法设置 MySQL 超时: " . $e->getMessage() . "\n\n";
}

// ============================================================
// 配置：新 term（英文为默认语言）=> 旧 term 的 tid 列表
//
// 新 term 编号对应：
//   1 = 数字前端系统集成 / Digital Frontend System Integration
//   2 = 直播与远程制作系统 / Live & Remote Production System
//   3 = 演播室与总控 / Studio & Master Control
//   4 = 传输与分发 / Transmission & Distribution
//   5 = 播出与存储 / Playout & Storage
//   6 = 监测及分析系统 / Monitoring & Analytics System
// ============================================================
$merge_map = [
  [
    'en'       => 'Digital Frontend System Integration',
    'zh'       => '数字前端系统集成',
    'old_tids' => [190, 185],   // 融媒体平台, 数字电视平台, 融媒体数字电视前端系统
  ],
  [
    'en'       => 'Live & Remote Production System',
    'zh'       => '直播与远程制作系统',
    'old_tids' => [95, 188],    // 5G移动新闻直播系统, 超高清直播
  ],
  [
    'en'       => 'Studio & Master Control',
    'zh'       => '演播室与总控',
    'old_tids' => [277, 96],    // 内部通讯/通话系统, 演播室系统与内部通话
  ],
  [
    'en'       => 'Transmission & Distribution',
    'zh'       => '传输与分发',
    'old_tids' => [99, 100, 187], // 射频切换分配系统, OTT多屏转码/分发系统, 视音频传输与分发
  ],
  [
    'en'       => 'Playout & Storage',
    'zh'       => '播出与存储',
    'old_tids' => [98, 186, 97],  // 哈雷视频产品, 总控播出系统, 电视台播出系统
  ],
  [
    'en'       => 'Monitoring & Analytics System',
    'zh'       => '监测及分析系统',
    'old_tids' => [189],          // 视音频分析与测试
  ],
];

// ============================================================
// 执行
// ============================================================

$term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');

foreach ($merge_map as $entry) {

  $new_term_en = $entry['en'];
  $new_term_zh = $entry['zh'];
  $old_tids    = $entry['old_tids'];

  // --- 1. 获取或创建新 term ---
  $existing_new = $term_storage->loadByProperties([
    'name' => $new_term_en,
    'vid'  => $vocabulary,
  ]);

  if ($existing_new) {
    $new_term = reset($existing_new);
    $new_tid  = $new_term->id();
    echo "[已存在] 新 term: \"{$new_term_en}\" (tid={$new_tid})\n";

    if (!$dry_run && !$new_term->hasTranslation('zh-hans')) {
      $new_term->addTranslation('zh-hans', ['name' => $new_term_zh])->save();
      echo "  [已添加] 中文翻译: \"{$new_term_zh}\"\n";
    }
    else {
      echo "  [已存在] 中文翻译: \"{$new_term_zh}\"\n";
    }
  }
  elseif ($dry_run) {
    echo "[DRY RUN] 将创建新 term: \"{$new_term_en}\" / \"{$new_term_zh}\"\n";
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

    $new_term->addTranslation('zh-hans', ['name' => $new_term_zh])->save();
    echo "  [已添加] 中文翻译: \"{$new_term_zh}\"\n";
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
      // 更新主字段表
      $count = $database->update($field_table)
        ->fields(['field_article_solution_target_id' => $new_tid])
        ->condition('field_article_solution_target_id', $old_tid)
        ->condition('bundle', 'article')
        ->execute();
      echo "    [更新] 主表 {$count} 条记录\n";

      // 同步 revision 表
      if ($database->schema()->tableExists($field_revision_table)) {
        $rcount = $database->update($field_revision_table)
          ->fields(['field_article_solution_target_id' => $new_tid])
          ->condition('field_article_solution_target_id', $old_tid)
          ->condition('bundle', 'article')
          ->execute();
        echo "    [更新] revision表 {$rcount} 条记录\n";
      }

      // 删除旧 term
      $old_term->delete();
      echo "    [已删除] tid={$old_tid} \"{$old_label}\"\n";
    }
  }

  echo "\n";
}

echo "==============================\n";
if ($dry_run) {
  echo "DRY RUN 完成！确认无误后去掉 --dry-run 正式运行。\n";
}
else {
  echo "全部完成！请手动执行: drush cr\n";
  echo "然后重建 taxonomy_index：\n";
  echo "drush php:eval \"\\\$nids = \\\Drupal::entityQuery('node')->condition('type','article')->condition('status',1)->accessCheck(FALSE)->execute(); \\\Drupal::database()->delete('taxonomy_index')->execute(); foreach(\\\$nids as \\\$nid){ \\\$node=\\\Drupal\\\node\\\Entity\\\Node::load(\\\$nid); taxonomy_build_node_index(\\\$node); } echo count(\\\$nids).' 个节点重建完成'.PHP_EOL;\"\n";
}
echo "==============================\n";
