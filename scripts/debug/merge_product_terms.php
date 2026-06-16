<?php

/**
 * Drush PHP script: 合并旧 product taxonomy terms 到新 term
 *
 * 用法:
 *   drush php:script scripts/debug/merge_product_terms.php
 *
 * 功能:
 *   1. 获取或创建新 term（zh-hans）
 *   2. 将所有 article 节点的 field_article_product 从旧 term 替换为新 term
 *   3. 删除旧 term
 */

use Drupal\taxonomy\Entity\Term;

$vocabulary = 'product';
$field_name = 'field_article_product';

// ============================================================
// 配置：新 term 名称 => [旧 term 的英文名称（数据库实际值）]
// ============================================================
$merge_map = [
  '直播编码与远程制作' => [
    '5G/4G Mobile Live Streaming Products',
    'Fully Integrated Networked Production and Broadcasting Products',
  ],
  '视音频处理' => [
    'Audio and Video Transmission Processing Products',
    'Ultra HD Encoding and Transcoding Products',
  ],
  '播出与存储' => [
    'Harmonic Video Product',
  ],
  '传输与分发' => [
    'Radio Frequency Optical Transmission',
    'Switch',
    'Comprehensive Reception Decoding Products',
    'Satellite RF-related Products',
    'ODF/Smart ODF Products',
    'Multi-Screen OTT Distribution System',
    'Embedded Platform',
    'Broadcast-grade adapter',
    'Digital/Analog Modulation Demodulation Products',
  ],
  '演播室系统' => [
    'Control Panel',
    'Internal Communication/Call System',
    'IPG Conversion',
    'Monitor',
    'SDN Orchestration Manager',
    'Switching Station / Virtual Switching Station',
    'Audio and Video Accessories',
    'Audio-Visual Matrix Products',
  ],
  '测试与监测' => [
    'Broadcast-grade test and measurement instruments',
  ],
];

// ============================================================
// 执行
// ============================================================

$term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
$node_storage = \Drupal::entityTypeManager()->getStorage('node');

foreach ($merge_map as $new_term_name => $old_term_names) {

  // --- 1. 获取或创建新 term（zh-hans） ---
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
      'name'     => $new_term_name,
      'vid'      => $vocabulary,
      'langcode' => 'zh-hans',
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
      echo "  [跳过] 旧 term \"{$old_term_name}\" 不存在\n";
      continue;
    }

    $old_term = reset($existing_old);
    $old_tid  = $old_term->id();
    echo "  [处理] \"{$old_term_name}\" (tid={$old_tid}) → \"{$new_term_name}\" (tid={$new_tid})\n";

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
          echo "    [更新] nid={$node->id()} \"{$node->label()}\"\n";
        }
      }
      echo "    [完成] 共更新 " . count($nodes) . " 个节点\n";
    }

    // --- 4. 删除旧 term ---
    $old_term->delete();
    echo "    [已删除] 旧 term \"{$old_term_name}\" (tid={$old_tid})\n";
  }

  echo "\n";
}

echo "==============================\n";
echo "全部完成！\n";
echo "==============================\n";
