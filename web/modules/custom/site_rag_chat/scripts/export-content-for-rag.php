<?php

/**
 * @file
 * 导出 Oneband 网站 article / page 节点的中文内容为纯文本文件。
 *
 * 用法（在项目根目录，跟 composer.json 同级的地方执行）：
 *   vendor/drush/drush/drush scr modules/custom/site_rag_chat/scripts/export-content-for-rag.php
 *
 * 如果 drush 本身可以直接调用，也可以用：
 *   drush scr scripts/export-content-for-rag.php
 *
 * 导出结果会写到 /tmp/rag-export/ 目录下，每个节点一个 .txt 文件，
 * 跑完脚本后会自动打包成 /tmp/rag-export.zip，方便你直接下载。
 */

use Drupal\node\Entity\Node;

// ---- 可按需调整的配置 ----
$content_types = ['article', 'page'];
$langcode = 'zh-hans';
$output_dir = '/tmp/rag-export';
$zip_path = '/tmp/rag-export.zip';
// --------------------------

if (!is_dir($output_dir)) {
  mkdir($output_dir, 0755, TRUE);
}
else {
  // 清空旧文件，避免重复导出堆积。
  array_map('unlink', glob($output_dir . '/*.txt'));
}

$query = \Drupal::entityQuery('node')
  ->condition('type', $content_types, 'IN')
  ->condition('status', 1)
  ->accessCheck(FALSE);
$nids = $query->execute();

$count = 0;
$skipped = 0;

foreach ($nids as $nid) {
  $node = Node::load($nid);
  if (!$node) {
    continue;
  }

  // 只要中文版本；如果该节点没有中文翻译，跳过。
  if (!$node->hasTranslation($langcode)) {
    $skipped++;
    continue;
  }
  $translation = $node->getTranslation($langcode);

  $title = $translation->getTitle();

  // 提取正文字段（大多数站点是 body，如果你的字段名不同请改这里）。
  $body_value = '';
  if ($translation->hasField('body') && !$translation->get('body')->isEmpty()) {
    $body_value = $translation->get('body')->value;
  }

  if (empty(trim($title)) && empty(trim($body_value))) {
    $skipped++;
    continue;
  }

  // 去 HTML 标签，解码实体，压缩多余空白。
  $plain_text = strip_tags($body_value);
  $plain_text = html_entity_decode($plain_text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  $plain_text = preg_replace('/[ \t]+/u', ' ', $plain_text);
  $plain_text = preg_replace('/\n{3,}/u', "\n\n", $plain_text);
  $plain_text = trim($plain_text);

  // 拼一个带标题和原文链接的完整文本，方便知识库里带上下文和可追溯来源。
  $url = $translation->toUrl('canonical', ['absolute' => TRUE])->toString();
  $content_type_label = $translation->bundle();

  $file_content = "标题：{$title}\n";
  $file_content .= "类型：{$content_type_label}\n";
  $file_content .= "原文链接：{$url}\n";
  $file_content .= "\n---\n\n";
  $file_content .= $plain_text;

  // 文件名：nid + 标题（做安全清理，避免特殊字符）。
  $safe_title = preg_replace('/[\/\\\\:\*\?"<>\|]+/u', '', $title);
  $safe_title = mb_substr($safe_title, 0, 60);
  $filename = sprintf('%s-%d-%s.txt', $content_type_label, $nid, $safe_title);

  file_put_contents($output_dir . '/' . $filename, $file_content);
  $count++;
}

// 打包成一个 zip，方便一次性下载。
if (class_exists('ZipArchive')) {
  $zip = new \ZipArchive();
  if ($zip->open($zip_path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
    $files = glob($output_dir . '/*.txt');
    foreach ($files as $file) {
      $zip->addFile($file, basename($file));
    }
    $zip->close();
  }
}

print "导出完成：成功 {$count} 篇，跳过（无中文翻译或内容为空）{$skipped} 篇。\n";
print "文本文件目录：{$output_dir}\n";
if (file_exists($zip_path)) {
  print "打包文件：{$zip_path}\n";
  print "可以用 scp 下载，例如：scp your_user@your_server:{$zip_path} ./\n";
}
