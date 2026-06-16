<?php

/**
 * Drush PHP script: 合并旧 product taxonomy terms 到新 term
 *
 * 用法:
 *   drush php:script scripts/debug/merge_product_terms.php
 *
 * 功能:
 *   1. 创建新 term（如已存在则复用）
 *   2. 将所有 article 节点的 field_article_product 从旧 term 替换为新 term
 *   3. 删除旧 term
 */

use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;

// ============================================================
// 配置：旧 term 名称 → 新 term 名称
// ============================================================
$merge_map = [
  // 新 term 名称 => [旧 term 名称列表]
  '直播编码与远程制作' => [
    '5G新闻采集',
    '全流程网络化制播产品',
  ],
  '视音频处理' => [
    '视音频处理',
    '超高清编转码产品',
  ],
  '播出与存储' => [
    '哈雷视频产品Harmonic Video Product',
  ],
  '传输与分发' => [
    '射频光传输',
    '交换机',
    '综合接收解码产品',
    '卫星射频相关产品',
    'ODF/智能ODF产品',
    '多屏OTT分发系统',
    '嵌入式平台',
    '广电级适配器',
    '数字/模拟调制解调产品',
  ],
  '演播室系统' => [
    '控制面板',
    '内部通讯/通话系统',
    'IPG转换',
    '监视器',
    'SDN编排管理器',
    '切换台/虚拟切换台',
    '视音频周边产品',
    '视音频矩阵产品',
  ],
  '测试与监测' => [
    '广播级测试测量仪器',
  ],
];

$vocabulary = 'product';
$field_name = 'field_article_product';

// ============================================================
// 执行
// ============================================================

$term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
$node_storage = \Drupal::entityTypeManager()->getStorage('node');

foreach ($merge_map as $new_term_name => $old_term_names) {

  // --- 1. 获取或创建新 term ---
  $existing_new = $term_storage->loadByProperties([
    'name' => $new_term_name,
    'vid'  => $vocabulary,
  ]);

  if ($existing_new) {
    $new_term = reset($existing_new);
    echo "[已存在] 新 term: \"{$new_term_name}\" (tid={$new_term->id()})\n";
  }
  else {
    $new_term = Term::create([
      'name' => $new_term_name,
      'vid'  => $vocabulary,
    ]);
    $new_term->save();
    echo "[已创建] 新 term: \"{$new_term_name}\" (tid={$new_term->id()})\n";
  }

  $new_tid = $new_term->id();

  // --- 2. 处理每一个旧 term ---
  foreach ($old_term_names as $old_term_name) {

    $existing_old = $term_storage->loadByProperties([
      'name' => $old_term_name,
      'vid'  => $vocabulary,
    ]);

    if (!$existing_old) {
      echo "  [跳过] 旧 term \"{$old_term_name}\" 不存在，忽略\n";
      continue;
    }

    $old_term = reset($existing_old);
    $old_tid  = $old_term->id();
    echo "  [处理] 旧 term: \"{$old_term_name}\" (tid={$old_tid}) → \"{$new_term_name}\"\n";

    // --- 3. 找到所有引用该旧 term 的 article 节点 ---
    $nids = $node_storage->getQuery()
      ->condition('type', 'article')
      ->condition($field_name, $old_tid)
      ->accessCheck(FALSE)
      ->execute();

    if (empty($nids)) {
      echo "    [无节点] 没有文章引用此旧 term\n";
    }
    else {
      $nodes = $node_storage->loadMultiple($nids);
      foreach ($nodes as $node) {
        $values = $node->get($field_name)->getValue();
        $updated = FALSE;

        // 替换旧 tid → 新 tid（保留字段中其他 term，去重）
        $new_values = [];
        $seen_tids  = [];
        foreach ($values as $item) {
          if ((int) $item['target_id'] === (int) $old_tid) {
            // 替换为新 tid
            if (!in_array($new_tid, $seen_tids)) {
              $new_values[] = ['target_id' => $new_tid];
              $seen_tids[]  = $new_tid;
            }
            $updated = TRUE;
          }
          else {
            // 保留其他 term（去重）
            if (!in_array($item['target_id'], $seen_tids)) {
              $new_values[] = $item;
              $seen_tids[]  = $item['target_id'];
            }
          }
        }

        if ($updated) {
          $node->set($field_name, $new_values);
          $node->save();
          echo "    [更新] nid={$node->id()} \"{$node->label()}\"\n";
        }
      }
      echo "    [完成] 共更新 " . count($nodes) . " 个节点\n";
    }

    // --- 4. 删除旧 term ---
    // 仅当旧 term 名称与新 term 名称不同时才删除（防止误删）
    if ($old_term_name !== $new_term_name) {
      $old_term->delete();
      echo "    [已删除] 旧 term \"{$old_term_name}\" (tid={$old_tid})\n";
    }
    else {
      echo "    [保留] term 名称未变，无需删除\n";
    }
  }

  echo "\n";
}

echo "==============================\n";
echo "全部完成！\n";
echo "==============================\n";
