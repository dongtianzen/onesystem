<?php

/**
 * Drush PHP script: 合并旧 product taxonomy terms 到新 term（双语，英文为默认）
 *
 * 用法:
 *   drush php:script scripts/debug/merge_product_terms.php
 *
 * Dry run（只打印，不写库）:
 *   drush php:script scripts/debug/merge_product_terms.php -- --dry-run
 */

use Drupal\taxonomy\Entity\Term;

$vocabulary = 'product';
$field_name = 'field_article_product';

$dry_run = in_array('--dry-run', $_SERVER['argv'] ?? []);

if ($dry_run) {
  echo "========== DRY RUN 模式（不会修改任何数据）==========\n\n";
}

// ============================================================
// 配置：新 term（英文为默认语言）=> 旧 term 的 tid 列表
// ============================================================
$merge_map = [
  [
    'en'       => 'Live Encoding & Remote Production',
    'zh'       => '直播编码与远程制作',
    'old_tids' => [85, 299],
  ],
  [
    'en'       => 'Audio & Video Processing',
    'zh'       => '视音频处理',
    'old_tids' => [84, 82],
  ],
  [
    'en'       => 'Playout & Storage',
    'zh'       => '播出与存储',
    'old_tids' => [287],
  ],
  [
    'en'       => 'Transmission & Distribution',
    'zh'       => '传输与分发',
    'old_tids' => [289, 303, 83, 88, 234, 93, 94, 92, 90],
  ],
  [
    'en'       => 'Studio Systems',
    'zh'       => '演播室系统',
    'old_tids' => [306, 275, 302, 304, 301, 305, 87, 86],
  ],
  [
    'en'       => 'Testing & Monitoring',
    'zh'       => '测试与监测',
    'old_tids' => [89],
  ],
];

// ============================================================
// 执行
// ============================================================

$term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
$node_storage = \Drupal::entityTypeManager()->getStorage('node');

foreach ($merge_map as $entry) {

  $new_term_en = $entry['en'];
  $new_term_zh = $entry['zh'];
  $old_tids    = $entry['old_tids'];

  // --- 1. 获取或创建新 term（英文为默认语言）---
  $existing_new = $term_storage->loadByProperties([
    'name' => $new_term_en,
    'vid'  => $vocabulary,
  ]);

  if ($existing_new) {
    $new_term = reset($existing_new);
    $new_tid  = $new_term->id();
    echo "[已存在] 新 term: \"{$new_term_en}\" (tid={$new_tid})\n";

    if (!$dry_run) {
      if (!$new_term->hasTranslation('zh-hans')) {
        $new_term->addTranslation('zh-hans', ['name' => $new_term_zh])->save();
        echo "  [已添加] 中文翻译: \"{$new_term_zh}\"\n";
      }
      else {
        echo "  [已存在] 中文翻译: \"{$new_term_zh}\"\n";
      }
    }
  }
  elseif ($dry_run) {
    echo "[DRY RUN] 将创建新 term: \"{$new_term_en}\" / \"{$new_term_zh}\"\n";
    $new_tid = 'NEW';
  }
  else {
    // 以 en 为默认语言创建
    $new_term = Term::create([
      'name'     => $new_term_en,
      'vid'      => $vocabulary,
      'langcode' => 'en',
    ]);
    $new_term->save();
    $new_tid = $new_term->id();
    echo "[已创建] 新 term: \"{$new_term_en}\" (tid={$new_tid})\n";

    // 添加中文翻译
    $new_term->addTranslation('zh-hans', ['name' => $new_term_zh])->save();
    echo "  [已添加] 中文翻译: \"{$new_term_zh}\"\n";
  }

  // --- 2. 处理每一个旧 term ---
  foreach ($old_tids as $old_tid) {

    $old_term = $term_storage->load($old_tid);

    if (!$old_term) {
      echo "  [跳过] tid={$old_tid} 不存在\n";
      continue;
    }

    $old_label = $old_term->label();
    echo "  [处理] tid={$old_tid} \"{$old_label}\" → \"{$new_term_en}\" (tid={$new_tid})\n";

    // --- 3. 找到所有引用该旧 term 的 article 节点 ---
    $nids = $node_storage->getQuery()
      ->condition('type', 'article')
      ->condition($field_name, $old_tid)
      ->accessCheck(FALSE)
      ->execute();

    if (empty($nids)) {
      echo "    [无节点] 没有文章引用 tid={$old_tid}\n";
    }
    else {
      echo "    [节点数] " . count($nids) . " 个节点需要更新\n";

      if (!$dry_run) {
        $nodes = $node_storage->loadMultiple($nids);
        foreach ($nodes as $node) {
          $values     = $node->get($field_name)->getValue();
          $new_values = [];
          $seen_tids  = [];
          $updated    = FALSE;

          foreach ($values as $item) {
            if ((int) $item['target_id'] === (int) $old_tid) {
              if (!in_array($new_tid, $seen_tids)) {
                $new_values[] = ['target_id' => $new_tid];
                $seen_tids[]  = $new_tid;
              }
              $updated = TRUE;
            }
            else {
              if (!in_array($item['target_id'], $seen_tids)) {
                $new_values[] = $item;
                $seen_tids[]  = $item['target_id'];
              }
            }
          }

          if ($updated) {
            $node->set($field_name, $new_values);
            $node->save();
            echo "    [更新] nid={$node->id()}\n";
          }
        }
      }
      else {
        foreach ($nids as $nid) {
          echo "    [DRY RUN] 将更新 nid={$nid}\n";
        }
      }
    }

    // --- 4. 删除旧 term ---
    if ($dry_run) {
      echo "    [DRY RUN] 将删除 tid={$old_tid} \"{$old_label}\"\n";
    }
    else {
      $old_term->delete();
      echo "    [已删除] tid={$old_tid} \"{$old_label}\"\n";
    }
  }

  echo "\n";
}

echo "==============================\n";
echo $dry_run ? "DRY RUN 完成！确认无误后去掉 --dry-run 正式运行。\n" : "全部完成！\n";
echo "==============================\n";
