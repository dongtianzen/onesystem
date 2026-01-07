<?php
/**
 * Step 02 (Local): Translate exported JSONL into English JSONL using OpenAI.
 *
 * ddev drush scr local_scripts/openai/step_02_translate_jsonl_openai.php
 *
 * Input JSONL line format (from Step 01):
 *  {
 *    "nid": 123,
 *    "type": "page",
 *    "lang": "en",
 *    "body_format": "basic_html",
 *    "title_src": "...(Chinese)...",
 *    "body_src":  "...(Chinese HTML)..."
 *  }
 *
 * Output JSONL line format:
 *  {
 *    "nid": 123,
 *    "lang": "en",
 *    "body_format": "basic_html",
 *    "title_new": "...(English)...",
 *    "body_new":  "...(English HTML, same structure)..."
 *  }
 *
 * Features:
 * - Keep HTML structure: translate text nodes only.
 * - Skip script/style/code/pre/textarea/etc.
 * - Resume support: if output file already contains nid, skip it.
 * - Print nid (one per line) after each successful translation.
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

// --------------------
// Parse CLI options
// --------------------
$options = getopt('', [
  'in:',
  'out:',
  'model::',
  'sleep::',
  'group-size::',
  'min-chars::',
]);

$inFile     = $options['in'] ?? '';
$outFile    = $options['out'] ?? '';
$model      = $options['model'] ?? 'gpt-4o-mini';
$sleep      = isset($options['sleep']) ? (float)$options['sleep'] : 0.2;
$groupSize  = isset($options['group-size']) ? (int)$options['group-size'] : 10;
$minChars   = isset($options['min-chars']) ? (int)$options['min-chars'] : 2;

if ($inFile === '' || $outFile === '') {
  fwrite(STDERR, "Usage: php step_02_translate_jsonl_openai.php --in=export_page_en.jsonl --out=translated.jsonl [--model=...] [--sleep=0.2]\n");
  exit(1);
}

if (!is_file($inFile)) {
  fwrite(STDERR, "Input file not found: $inFile\n");
  exit(1);
}

// --------------------
// API key from env
// --------------------
$apiKey = getenv('OPENAI_API_KEY');
if (!$apiKey) {
  fwrite(STDERR, "OPENAI_API_KEY is empty. Run: export OPENAI_API_KEY=...\n");
  exit(1);
}

$DELIM = "\n---__NODE_DELIM__---\n";
$SKIP_TAGS = ['script','style','noscript','iframe','code','pre','textarea'];

// --------------------
// Resume: load done nids
// --------------------
function load_done_nids(string $outFile): array {
  $done = [];
  if (!is_file($outFile)) return $done;

  $fh = fopen($outFile, 'r');
  if (!$fh) return $done;

  while (($line = fgets($fh)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $obj = json_decode($line, true);
    if (is_array($obj) && isset($obj['nid'])) {
      $done[(int)$obj['nid']] = true;
    }
  }
  fclose($fh);
  return $done;
}

// --------------------
// OpenAI translate (plain text)
// --------------------
function ai_translate(string $text, string $apiKey, string $model): string {
  $text = trim($text);
  if ($text === '') return '';

  $payload = [
    'model' => $model,
    'temperature' => 0.2,
    'messages' => [
      [
        'role' => 'system',
        'content' =>
          'Translate the following Chinese text into professional, natural English. ' .
          'Keep product names and model numbers unchanged. ' .
          'Keep URLs unchanged. ' .
          'Preserve line breaks and segment order. ' .
          'Return only the translation.',
      ],
      ['role' => 'user', 'content' => $text],
    ],
  ];

  $ch = curl_init('https://api.openai.com/v1/chat/completions');
  curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
      'Authorization: Bearer ' . $apiKey,
      'Content-Type: application/json',
    ],
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_TIMEOUT => 120,
  ]);

  $raw  = curl_exec($ch);
  $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err  = curl_error($ch);
  curl_close($ch);

  if ($raw === false) {
    throw new Exception("OpenAI cURL error: $err");
  }

  $res = json_decode($raw, true);
  if ($http >= 400 || isset($res['error'])) {
    $msg = $res['error']['message'] ?? substr($raw, 0, 600);
    throw new Exception("OpenAI API error: $msg");
  }

  return trim($res['choices'][0]['message']['content'] ?? '');
}

// --------------------
// Translate HTML text nodes only (keep structure)
// --------------------
function translate_html_text_nodes(
  string $html,
  string $apiKey,
  string $model,
  string $DELIM,
  array $SKIP_TAGS,
  int $groupSize = 10,
  int $minChars = 2,
  float $sleep = 0.2
): string {
  if (trim(strip_tags($html)) === '') {
    return $html;
  }

  $wrapped = '<div id="__wrap__">' . $html . '</div>';

  $dom = new DOMDocument('1.0', 'UTF-8');
  libxml_use_internal_errors(true);
  $dom->loadHTML(mb_convert_encoding($wrapped, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
  libxml_clear_errors();

  $xpath = new DOMXPath($dom);

  // Build XPath condition to exclude skip tags
  $conds = [];
  foreach ($SKIP_TAGS as $t) {
    $t = strtolower(trim($t));
    if ($t !== '') $conds[] = "not(ancestor::{$t})";
  }
  $cond = $conds ? ' and ' . implode(' and ', $conds) : '';

  $nodes = $xpath->query("//text()[normalize-space() != ''{$cond}]");
  if (!$nodes || $nodes->length === 0) {
    return $html;
  }

  $textNodes = [];
  foreach ($nodes as $n) {
    /** @var DOMText $n */
    $plain = trim($n->nodeValue);
    if ($plain === '') continue;
    if (mb_strlen($plain, 'UTF-8') < $minChars) continue;

    // Only translate meaningful nodes (letters or CJK)
    if (!preg_match('/[A-Za-z\x{4E00}-\x{9FFF}]/u', $plain)) continue;

    $textNodes[] = $n;
  }

  $total = count($textNodes);
  if ($total === 0) return $html;

  for ($i = 0; $i < $total; $i += $groupSize) {
    $chunk = array_slice($textNodes, $i, $groupSize);

    $parts = [];
    foreach ($chunk as $tn) {
      $parts[] = trim($tn->nodeValue);
    }

    $joined = implode($DELIM, $parts);

    $translatedJoined = ai_translate($joined, $apiKey, $model);
    usleep((int)round($sleep * 1_000_000));

    $translatedParts = explode($DELIM, $translatedJoined);

    // If mismatch, fallback to per-node translation to avoid misalignment
    if (count($translatedParts) !== count($chunk)) {
      $translatedParts = [];
      foreach ($parts as $p) {
        $translatedParts[] = ai_translate($p, $apiKey, $model);
        usleep((int)round($sleep * 1_000_000));
      }
    }

    foreach ($chunk as $idx => $tn) {
      $newText = trim($translatedParts[$idx] ?? '');
      if ($newText !== '') {
        $tn->nodeValue = $newText;
      }
    }
  }

  $wrap = $dom->getElementById('__wrap__');
  if (!$wrap) return $html;

  $out = '';
  foreach ($wrap->childNodes as $child) {
    $out .= $dom->saveHTML($child);
  }

  return $out;
}

// --------------------
// Main
// --------------------
$done = load_done_nids($outFile);

$in  = fopen($inFile, 'r');
if (!$in) {
  fwrite(STDERR, "Cannot open input file: $inFile\n");
  exit(1);
}

$out = fopen($outFile, 'a');
if (!$out) {
  fclose($in);
  fwrite(STDERR, "Cannot open output file for append: $outFile\n");
  exit(1);
}

while (($line = fgets($in)) !== false) {
  $line = trim($line);
  if ($line === '') continue;

  $row = json_decode($line, true);
  if (!is_array($row)) continue;

  $nid = (int)($row['nid'] ?? 0);
  if ($nid <= 0) continue;

  // Resume support
  if (isset($done[$nid])) {
    continue;
  }

  $titleSrc = (string)($row['title_src'] ?? '');
  $bodySrc  = (string)($row['body_src'] ?? '');
  $fmt      = (string)($row['body_format'] ?? 'basic_html');
  $lang     = (string)($row['lang'] ?? 'en');

  try {
    $titleNew = ai_translate($titleSrc, $apiKey, $model);
    usleep((int)round($sleep * 1_000_000));

    $bodyNew = translate_html_text_nodes(
      $bodySrc, $apiKey, $model, $DELIM, $SKIP_TAGS, $groupSize, $minChars, $sleep
    );
    usleep((int)round($sleep * 1_000_000));

    $outRow = [
      'nid' => $nid,
      'lang' => $lang,
      'body_format' => $fmt,
      'title_new' => $titleNew,
      'body_new' => $bodyNew,
    ];

    fwrite($out, json_encode($outRow, JSON_UNESCAPED_UNICODE) . "\n");
    fflush($out);

    // Print nid one per line (as requested)
    echo $nid . PHP_EOL;

    $done[$nid] = true;
  }
  catch (Throwable $e) {
    // Print errors to STDERR but continue
    fwrite(STDERR, "nid={$nid} FAILED: " . $e->getMessage() . "\n");
    // Do not mark done, so you can rerun and resume
    continue;
  }
}

fclose($in);
fclose($out);
