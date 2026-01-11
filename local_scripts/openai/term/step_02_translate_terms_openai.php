<?php

/**
 * Step 2: Translate terms JSONL (ZH -> EN) using OpenAI Responses API.
 *
 * Input JSONL: each line has {tid, vid, name, description: {value, format}, src_lang, dst_lang}
 * Output JSONL: same structure + translated fields put into:
 *   - name_translated
 *   - description_translated.value
 *
 * Run:
 *  export OPENAI_API_KEY="..."
 *  php local_scripts/openai/term/step_02_translate_terms_openai.php
 */

$IN_FILE   = 'web/sites/default/files/private/translate/terms_export_zh.jsonl';
$OUT_FILE  = 'web/sites/default/files/private/translate/terms_translated_en.jsonl';

$MODEL     = 'gpt-4o-mini';     // 你也可以换成你在用的模型
$SLEEP_SEC = 0.2;               // 防抖
$MIN_CHARS = 2;                 // 小于这个就跳过翻译
$MAX_RETRY = 5;

$API_KEY = getenv('OPENAI_API_KEY');
if (!$API_KEY) {
  fwrite(STDERR, "Missing env OPENAI_API_KEY\n");
  exit(1);
}

function norm_text($s): string {
  $s = (string) $s;
  $s = str_replace(["\r\n", "\r"], "\n", $s);
  return trim($s);
}

function call_openai_translate(string $apiKey, string $model, string $text, string $srcLang, string $dstLang): string {
  // Responses API: https://api.openai.com/v1/responses  :contentReference[oaicite:1]{index=1}
  $url = 'https://api.openai.com/v1/responses';

  $system = "You are a professional translator for a Drupal bilingual website.";
  $user = "Translate from {$srcLang} to {$dstLang}.\n"
        . "Requirements:\n"
        . "- Keep meaning accurate, natural for website taxonomy terms.\n"
        . "- Do NOT add explanations.\n"
        . "- Preserve line breaks.\n"
        . "- If the input is empty, return empty.\n\n"
        . "TEXT:\n{$text}";

  $payload = [
    'model' => $model,
    'input' => [
      ['role' => 'system', 'content' => $system],
      ['role' => 'user', 'content' => $user],
    ],
  ];

  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
      'Content-Type: application/json',
      'Authorization: Bearer ' . $apiKey,
    ],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    CURLOPT_TIMEOUT => 120,
  ]);

  $resp = curl_exec($ch);
  $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

  if ($resp === false) {
    $err = curl_error($ch);
    curl_close($ch);
    throw new RuntimeException("curl error: $err");
  }
  curl_close($ch);

  if ($httpCode < 200 || $httpCode >= 300) {
    throw new RuntimeException("OpenAI HTTP $httpCode: " . substr($resp, 0, 4000));
  }

  $json = json_decode($resp, true);
  if (!is_array($json)) {
    throw new RuntimeException("Invalid JSON response: " . substr($resp, 0, 2000));
  }

  // Extract text from Responses API output.
  // Typical shape includes output -> [{content:[{type:"output_text", text:"..."}]}]
  $outText = '';
  if (!empty($json['output']) && is_array($json['output'])) {
    foreach ($json['output'] as $item) {
      if (!empty($item['content']) && is_array($item['content'])) {
        foreach ($item['content'] as $c) {
          if (($c['type'] ?? '') === 'output_text' && isset($c['text'])) {
            $outText .= $c['text'];
          }
        }
      }
    }
  }

  $outText = norm_text($outText);
  return $outText;
}

$in = fopen($IN_FILE, 'rb');
if (!$in) { fwrite(STDERR, "Cannot open IN_FILE: $IN_FILE\n"); exit(1); }
$out = fopen($OUT_FILE, 'wb');
if (!$out) { fwrite(STDERR, "Cannot open OUT_FILE: $OUT_FILE\n"); exit(1); }

$lineNo = 0;
while (($line = fgets($in)) !== false) {
  $lineNo++;
  $line = trim($line);
  if ($line === '') continue;

  $row = json_decode($line, true);
  if (!is_array($row)) {
    fwrite(STDERR, "Skip invalid JSON at line $lineNo\n");
    continue;
  }

  $srcLang = (string)($row['src_lang'] ?? 'zh-hans');
  $dstLang = (string)($row['dst_lang'] ?? 'en');

  $name = norm_text((string)($row['name'] ?? ''));
  $desc = norm_text((string)(($row['description']['value'] ?? ''));

  // Translate name
  $nameTr = '';
  if (mb_strlen($name) >= $MIN_CHARS) {
    for ($i=1; $i<=$MAX_RETRY; $i++) {
      try {
        $nameTr = call_openai_translate($API_KEY, $MODEL, $name, $srcLang, $dstLang);
        break;
      } catch (Throwable $e) {
        fwrite(STDERR, "Name translate error line $lineNo try $i: {$e->getMessage()}\n");
        usleep(300000 * $i);
      }
    }
  }

  // Translate description
  $descTr = '';
  if (mb_strlen($desc) >= $MIN_CHARS) {
    for ($i=1; $i<=$MAX_RETRY; $i++) {
      try {
        $descTr = call_openai_translate($API_KEY, $MODEL, $desc, $srcLang, $dstLang);
        break;
      } catch (Throwable $e) {
        fwrite(STDERR, "Desc translate error line $lineNo try $i: {$e->getMessage()}\n");
        usleep(300000 * $i);
      }
    }
  }

  $row['name_translated'] = $nameTr;
  $row['description_translated'] = [
    'value' => $descTr,
    'format' => (string)($row['description']['format'] ?? 'basic_html'),
  ];

  fwrite($out, json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");

  if ($SLEEP_SEC > 0) usleep((int)($SLEEP_SEC * 1000000));
}

fclose($in);
fclose($out);

fwrite(STDERR, "Done. Output: $OUT_FILE\n");
