<?php

use Drupal\node\Entity\Node;

/**
 * 批量修复：zh-hans 和 en 都存在，但 en 仍是中文（或与 zh-hans 相同）
 * - title：翻译纯文本
 * - body：保留 HTML 结构，只翻译文本节点
 * - 记录并输出“翻译成功的 nid 列表”，每行一个 nid
 *
 * 用法：
 *  ddev drush scr drush/scripts/step_03_translate_bulk_fix_en_keep_html.php \
 *    --source=zh-hans --target=en --types=page,article --batch=20 --sleep=0.3
 *
 * Dry run（只打印不保存）：
 *  ddev drush scr drush/scripts/step_03_translate_bulk_fix_en_keep_html.php \
 *    --dry-run=1
 *
 * 断点续跑：
 *  ddev drush scr drush/scripts/step_03_translate_bulk_fix_en_keep_html.php \
 *    --start-nid=1200
 *
 * 强制覆盖所有候选（慎用）：
 *  ddev drush scr drush/scripts/step_03_translate_bulk_fix_en_keep_html.php \
 *    --force=1
 */

// ----------------------
// 读取参数
// ----------------------
$options = \Drush\Drush::input()->getOptions();

$source    = (string) ($options['source'] ?? 'zh-hans');
$target    = (string) ($options['target'] ?? 'en');
$types_raw = (string) ($options['types']  ?? 'page,article');
$types     = array_values(array_filter(array_map('trim', explode(',', $types_raw))));

$limit      = (int)   ($options['limit'] ?? 1);          // 0=不限制
$batch      = (int)   ($options['batch'] ?? 20);         // 每批加载多少个 node
$sleep      = (float) ($options['sleep'] ?? 0.3);        // 每次 OpenAI 请求间隔（秒）
$start_nid  = (int)   ($options['start-nid'] ?? 0);      // 从某 nid 之后开始
$dry_run    = (bool)  ($options['dry-run'] ?? false);    // 1=不保存
$force      = (bool)  ($options['force'] ?? false);      // 1=不检测，直接重翻译覆盖 en（慎用）
print('dry_run is ' . $dry_run);
print('limit is ' . $limit);


$limit = 1;
$dry_run = 1;

print('dry_run v2 is ' . $dry_run);
print('limit v2 is ' . $limit);

// HTML 翻译选项
$html_group_size = (int)($options['html-group-size'] ?? 10); // 每次合并翻译多少个文本节点
$html_min_chars  = (int)($options['html-min-chars'] ?? 2);   // 片段太短不翻译

if (empty($types)) throw new \Exception("Missing --types (e.g. --types=page,article)");
if ($source === $target) throw new \Exception("--source and --target cannot be the same.");

// ----------------------
// API Key（必须来自环境变量）
// ----------------------
$api_key = getenv('OPENAI_API_KEY');
if (empty($api_key)) {
  throw new \Exception("OPENAI_API_KEY is empty. Set it in your ddev container env.");
}

$logger = \Drush\Drush::logger();

$logger->notice("Source={$source}, Target={$target}, Types=" . implode(',', $types)
  . ", Force=" . ($force ? '1' : '0')
  . ", DryRun=" . ($dry_run ? '1' : '0')
  . ", Limit=" . ($limit ?: '0(unlimited)')
  . ", Batch={$batch}, Sleep={$sleep}s"
  . ", StartNid>{$start_nid}"
  . ", HtmlGroupSize={$html_group_size}, HtmlMinChars={$html_min_chars}"
);

// ----------------------
// 辅助：判断中文特征（启发式）
// ----------------------
function looks_like_chinese(string $text): bool {
  $text = trim(strip_tags($text));
  if ($text === '') return false;

  preg_match_all('/[\x{4E00}-\x{9FFF}\x{3400}-\x{4DBF}\x{F900}-\x{FAFF}]/u', $text, $m);
  $cjk = count($m[0]);

  preg_match_all('/[A-Za-z0-9]/', $text, $m2);
  $latin = count($m2[0]);

  if ($cjk >= 10 && $cjk > $latin) return true;
  if (preg_match('/[，。；：？！【】（）《》]/u', $text)) return true;

  return false;
}

function norm_text(string $s): string {
  $s = strip_tags($s);
  $s = preg_replace('/\s+/u', ' ', $s);
  return trim((string) $s);
}

// 是否需要重翻译：目标为空/与源一致/目标像中文
function need_retranslate(string $src, string $dst): bool {
  $src_n = norm_text($src);
  $dst_n = norm_text($dst);

  if ($dst_n === '') return true;
  if ($src_n !== '' && $src_n === $dst_n) return true;
  if (looks_like_chinese($dst)) return true;

  return false;
}

// ----------------------
// OpenAI：翻译纯文本（title/分片）
// ----------------------
function ai_translate(string $text, string $api_key): string {
  $text = trim($text);
  if ($text === '') return '';

  $payload = [
    'model' => 'gpt-4o-mini',
    'messages' => [
      [
        'role' => 'system',
        'content' => 'Translate the following Chinese text into professional, natural English. Keep product names and model numbers unchanged. Keep URLs unchanged. Preserve line breaks and segment order. Do not add numbering or extra commentary. Return only the translation.',
      ],
      ['role' => 'user', 'content' => $text],
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
    CURLOPT_TIMEOUT => 90,
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
    $msg = $res['error']['message'] ?? substr($raw, 0, 600);
    throw new \Exception("OpenAI API error: $msg");
  }

  return trim($res['choices'][0]['message']['content'] ?? '');
}

/**
 * 保留 HTML 标签结构，只翻译文本节点
 */
function translate_html_text_nodes(string $html, string $api_key, array $opts = []): string {
  $html = (string) $html;

  if (trim(strip_tags($html)) === '') {
    return $html;
  }

  $skip_tags  = $opts['skip_tags']  ?? ['script','style','noscript','iframe','code','pre','textarea'];
  $group_size = (int)($opts['group_size'] ?? 10);
  $min_chars  = (int)($opts['min_chars']  ?? 2);
  $sleep      = (float)($opts['sleep']    ?? 0.3);

  $wrapped = '<div id="__wrap__">' . $html . '</div>';

  $dom = new \DOMDocument('1.0', 'UTF-8');
  libxml_use_internal_errors(true);

  $dom->loadHTML(mb_convert_encoding($wrapped, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

  libxml_clear_errors();

  $xpath = new \DOMXPath($dom);

  $conditions = [];
  foreach ($skip_tags as $t) {
    $t = strtolower(trim($t));
    if ($t !== '') {
      $conditions[] = "not(ancestor::{$t})";
    }
  }
  $cond = $conditions ? ' and ' . implode(' and ', $conditions) : '';

  $nodes = $xpath->query("//text()[normalize-space() != ''{$cond}]");
  if (!$nodes || $nodes->length === 0) {
    return $html;
  }

  $textNodes = [];
  foreach ($nodes as $n) {
    /** @var \DOMText $n */
    $plain = trim($n->nodeValue);

    if (mb_strlen($plain, 'UTF-8') < $min_chars) continue;
    if (!preg_match('/[A-Za-z\x{4E00}-\x{9FFF}]/u', $plain)) continue;

    $textNodes[] = $n;
  }

  if (count($textNodes) === 0) {
    return $html;
  }

  $DELIM = "\n---__NODE_DELIM__---\n";
  $total = count($textNodes);

  for ($i = 0; $i < $total; $i += $group_size) {
    $chunk = array_slice($textNodes, $i, $group_size);

    $parts = [];
    foreach ($chunk as $tn) {
      $parts[] = trim($tn->nodeValue);
    }

    $joined = implode($DELIM, $parts);

    $translatedJoined = ai_translate($joined, $api_key);
    usleep((int) round($sleep * 1_000_000));

    $translatedParts = explode($DELIM, $translatedJoined);

    // 段数不匹配：退化为逐个翻译，保证不乱写 DOM
    if (count($translatedParts) !== count($chunk)) {
      $translatedParts = [];
      foreach ($parts as $p) {
        $translatedParts[] = ai_translate($p, $api_key);
        usleep((int) round($sleep * 1_000_000));
      }
    }

    foreach ($chunk as $idx => $tn) {
      $newText = trim($translatedParts[$idx] ?? '');
      if ($newText === '') continue;
      $tn->nodeValue = $newText;
    }
  }

  $wrap = $dom->getElementById('__wrap__');
  if (!$wrap) {
    return $html;
  }

  $out = '';
  foreach ($wrap->childNodes as $child) {
    $out .= $dom->saveHTML($child);
  }

  return $out;
}

// ----------------------
// 查找候选 NIDs（按 type）
// ----------------------
$query = \Drupal::entityQuery('node')
  ->accessCheck(FALSE)
  ->condition('type', $types, 'IN')
  ->sort('nid', 'ASC');

if ($start_nid > 0) $query->condition('nid', $start_nid, '>');
if ($limit > 0) $query->range(0, $limit);

$nids = array_values($query->execute());
$total = count($nids);

if ($total === 0) {
  $logger->notice("No nodes found.");
  return;
}

$logger->notice("Found {$total} candidate nodes.");

$storage = \Drupal::entityTypeManager()->getStorage('node');

$processed = 0;
$updated   = 0;
$skipped   = 0;
$failed    = 0;

// ✅ 记录翻译/更新成功的 nid（仅在非 dry-run 且 save 成功时记录）
$translated_nids = [];

for ($i = 0; $i < $total; $i += $batch) {
  $slice = array_slice($nids, $i, $batch);
  $nodes = $storage->loadMultiple($slice);

  foreach ($slice as $nid) {
    $processed++;

    /** @var \Drupal\node\Entity\Node|null $node */
    $node = $nodes[$nid] ?? null;
    if (!$node) {
      $failed++;
      $logger->warning("[{$processed}/{$total}] nid={$nid} not loaded");
      continue;
    }

    if (!$node->hasTranslation($source)) {
      $skipped++;
      $logger->info("[{$processed}/{$total}] nid={$nid} skip: no {$source}");
      continue;
    }

    if (!$node->hasTranslation($target)) {
      $skipped++;
      $logger->info("[{$processed}/{$total}] nid={$nid} skip: no {$target}");
      continue;
    }

    $src = $node->getTranslation($source);
    $dst = $node->getTranslation($target);

    // 源字段
    $title_zh = (string) $src->label();
    $body_zh  = $src->hasField('body') ? (string) $src->get('body')->value : '';
    $body_fmt = $src->hasField('body') ? (string) $src->get('body')->format : 'basic_html';

    // 目标当前内容
    $title_en_now = (string) $dst->label();
    $body_en_now  = $dst->hasField('body') ? (string) $dst->get('body')->value : '';

    $need_title = $force ? true : need_retranslate($title_zh, $title_en_now);
    $need_body  = $force ? true : need_retranslate($body_zh,  $body_en_now);

    if (!$need_title && !$need_body) {
      $skipped++;
      $logger->info("[{$processed}/{$total}] nid={$nid} skip: target looks already English");
      continue;
    }

    try {
      $new_title = $title_en_now;
      if ($need_title) {
        $new_title = ai_translate($title_zh, $api_key);
        usleep((int) round($sleep * 1_000_000));
        if (trim($new_title) === '') $new_title = $title_zh;
      }

      $new_body = $body_en_now;
      if ($dst->hasField('body') && $need_body) {
        $new_body = translate_html_text_nodes($body_zh, $api_key, [
          'group_size' => $html_group_size,
          'min_chars'  => $html_min_chars,
          'sleep'      => $sleep,
          'skip_tags'  => ['script','style','noscript','iframe','code','pre','textarea'],
        ]);
      }

      if ($need_title) {
        $dst->setTitle($new_title);
      }

      if ($dst->hasField('body') && $need_body) {
        $dst->set('body', [
          'value'  => $new_body,
          'format' => $body_fmt,
        ]);
      }

      if ($dry_run) {
        $logger->notice("[{$processed}/{$total}] nid={$nid} DRY-RUN would update (title=" . ($need_title?'Y':'N') . ", body=" . ($need_body?'Y':'N') . ")");
      } else {
        $node->save();
        $updated++;
        $translated_nids[] = $nid; // ✅ 记录
        $logger->notice("[{$processed}/{$total}] nid={$nid} UPDATED en (title=" . ($need_title?'Y':'N') . ", body=" . ($need_body?'Y':'N') . ")");
      }
    }
    catch (\Throwable $e) {
      $failed++;
      $logger->error("[{$processed}/{$total}] nid={$nid} FAILED: " . $e->getMessage());
      continue;
    }
  }

  $logger->notice("Batch done. processed={$processed}/{$total}, updated={$updated}, skipped={$skipped}, failed={$failed}");
}

// ----------------------
// ✅ 最终输出：每行一个 nid
// ----------------------
if (!$dry_run) {
  if (!empty($translated_nids)) {
    $logger->notice("📌 Translated node IDs (" . count($translated_nids) . "), one per line:");
    foreach ($translated_nids as $id) {
      // 每行一个 nid
      print $id . PHP_EOL;
    }
  } else {
    $logger->notice("📌 No nodes were translated.");
  }
} else {
  $logger->notice("Dry-run mode: no nodes saved, no translated nid list printed.");
}

$logger->notice("✅ Done. total={$total}, updated={$updated}, skipped={$skipped}, failed={$failed}");
