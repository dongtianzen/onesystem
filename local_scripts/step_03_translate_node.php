<?php

use Drupal\node\Entity\Node;

/**
 * 用 OpenAI 翻译指定 node 的某个语言到目标语言
 * 用法：
 *   drush scr local_scripts/step_03_translate_node.php --nid=403 --source=zh-hans --target=en
 */

// ----------------------
// 读取参数
// ----------------------
$nid    = 403;
$source = 'zh-hans';
$target = 'en';

if (!$nid) {
  throw new \Exception("Missing --nid parameter");
}

// ----------------------
// API Key
// ----------------------
$api_key = 'sk-proj';
if (empty($api_key)) {
  throw new \Exception("OPENAI_API_KEY is empty. Make sure it exists inside ddev container.");
}

// ----------------------
// 加载 Node
// ----------------------
$node = Node::load($nid);
if (!$node) {
  throw new \Exception("Node $nid not found");
}

if (!$node->hasTranslation($source)) {
  throw new \Exception("Source language [$source] not found on node $nid");
}

$src = $node->getTranslation($source);

// ----------------------
// 创建 / 获取目标翻译
// ----------------------
if (!$node->hasTranslation($target)) {
  $dst = $node->addTranslation($target, $src->toArray());

  // 继承发布状态 / 工作流
  $dst->setPublished($src->isPublished());
  if ($dst->hasField('moderation_state') && $src->hasField('moderation_state')) {
    $dst->set('moderation_state', $src->get('moderation_state')->value);
  }
} else {
  $dst = $node->getTranslation($target);
}

// ----------------------
// 取字段
// ----------------------
$title_zh = trim((string) $src->label());
$body_zh  = $src->hasField('body') ? (string) $src->get('body')->value : '';
$body_fmt = $src->hasField('body') ? (string) $src->get('body')->format : 'basic_html';

if ($title_zh === '') {
  throw new \Exception("Source title is empty (node $nid, lang $source)");
}

// ----------------------
// 翻译函数
// ----------------------
function ai_translate(string $text, string $api_key): string {
  if (trim($text) === '') {
    return '';
  }

  $payload = [
    'model' => 'gpt-4o-mini',
    'messages' => [
      [
        'role' => 'system',
        'content' => 'Translate the following Chinese text into professional, natural English. Keep product names and model numbers unchanged. Return only the translation.',
      ],
      [
        'role' => 'user',
        'content' => $text,
      ],
    ],
    'temperature' => 0.2,
  ];

  $ch = curl_init('https://api.openai.com/v1/chat/completions');
  curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
      'Authorization: Bearer ' . $api_key,
      'Content-Type: application/json',
    ],
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_TIMEOUT => 60,
  ]);

  $raw  = curl_exec($ch);
  $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err  = curl_error($ch);
  curl_close($ch);

  if ($raw === false) {
    throw new \Exception("OpenAI cURL error: $err");
  }

  $res = json_decode($raw, true);

  if ($http >= 400 || isset($res['error'])) {
    $msg = $res['error']['message'] ?? substr($raw, 0, 300);
    throw new \Exception("OpenAI API error: $msg");
  }

  return trim($res['choices'][0]['message']['content'] ?? '');
}

// ----------------------
// 执行翻译
// ----------------------
$title_en = ai_translate($title_zh, $api_key);
$body_en  = ai_translate($body_zh,  $api_key);

// title 兜底
if (trim($title_en) === '') {
  $title_en = $title_zh;
}

// ----------------------
// 写回 Node
// ----------------------
$dst->setTitle($title_en);

if ($dst->hasField('body')) {
  $dst->set('body', [
    'value'  => $body_en,
    'format' => $body_fmt,
  ]);
}

$node->save();

print "✅ Translated node $nid ($source → $target)\n";
