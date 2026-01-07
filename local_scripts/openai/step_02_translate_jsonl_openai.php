<?php
/**
 * Step 02 (Local): Translate exported JSONL into English JSONL using OpenAI.
 *
 * ✅ No CLI args needed. All paths/config are hard-coded below.
 *
 * Recommended run (inside ddev container, without drush):
 *   ddev exec -T php local_scripts/openai/step_02_translate_jsonl_openai.php
 *
 * You CAN run with drush scr too (no args):
 *   ddev drush scr local_scripts/openai/step_02_translate_jsonl_openai.php
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

/* =========================
 *  CONFIG (NO ARGS)
 * ========================= */

// Input/Output JSONL paths (inside project / container)
$IN_FILE  = 'web/sites/default/files/private/translate/export_page_en.jsonl';
$OUT_FILE = 'web/sites/default/files/private/translate/translated_page_en.jsonl';

// OpenAI model
$MODEL = 'gpt-4o-mini';

// Sleep between API calls (seconds)
$SLEEP = 0.2;

// For HTML: translate text nodes in groups to reduce API calls
$HTML_GROUP_SIZE = 10;

// Ignore very short text nodes
$HTML_MIN_CHARS = 2;

// Delimiter used to join multiple text segments in one API call
$DELIM = "\n---__NODE_DELIM__---\n";

// Tags to skip translating inside
$SKIP_TAGS = ['script','style','noscript','iframe','code','pre','textarea'];

/* =========================
 *  PRECHECKS
 * ========================= */

if (!is_file($IN_FILE)) {
  fwrite(STDERR, "Input file not found: {$IN_FILE}\n");
  exit(1);
}

// API key from env (inside container/local env)
$apiKey = getenv('OPENAI_API_KEY');
if (!$apiKey) {
  fwrite(STDERR, "OPENAI_API_KEY is empty.\n");
  fwrite(STDERR, "If using ddev, run like:\n");
  fwrite(STDERR, "  ddev exec -T bash -lc 'export OPENAI_API_KEY=\"...\"; php local_scripts/openai/step_02_translate_jsonl_openai.php'\n");
  exit(1);
}

fwrite(STDERR, "Using IN : {$IN_FILE}\n");
fwrite(STDERR, "Using OUT: {$OUT_FILE}\n");
fwrite(STDERR, "Using MODEL={$MODEL}, SLEEP={$SLEEP}, HTML_GROUP_SIZE={$HTML_GROUP_SIZE}, HTML_MIN_CHARS={$HTML_MIN_CHARS}\n");

/* =========================
 *  HELPERS
 * ========================= */

/**
 * Resume support: load done nids from existing output JSONL.
 */
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

/**
 * Call OpenAI Chat Completions to translate plain text (no HTML).
 */
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

/**
 * Translate HTML by translating only text nodes (keep tags/attributes as-is).
 */
function translate_html_text_nodes(
  string $html,
  string $apiKey,
  string $model,
  string $DELIM,
  array $SKIP_TAGS,
  int $groupSize,
  int $minChars,
  float $sleep
): string {
  // If no meaningful text, return as-is.
  if (trim(strip_tags($html)) === '') {
    return $html;
  }

  // Wrap to keep a stable root node
  $wrapped = '<div id="__wrap__">' . $html . '</div>';

  $dom = new DOMDocument('1.0', 'UTF-8');
  libxml_use_internal_errors(true);
  $dom->loadHTML(
    mb_convert_encoding($wrapped, 'HTML-ENTITIES', 'UTF-8'),
    LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
  );
  libxml_clear_errors();

  $xpath = new DOMXPath($dom);

  // Build XPath condition to exclude skip tags via ancestor check
  $conds = [];
  foreach ($SKIP_TAGS as $t) {
    $t = strtolower(trim($t));
    if ($t !== '') $conds[] = "not(ancestor::{$t})";
  }
  $cond = $conds ? ' and ' . implode(' and ', $conds) : '';

  // All non-empty text nodes not under skip tags
  $nodes = $xpath->query("//text()[normalize-space() != ''{$cond}]");
  if (!$nodes || $nodes->length === 0) {
    return $html;
  }

  // Filter to meaningful text nodes
  $textNodes = [];
  foreach ($nodes as $n) {
    /** @var DOMText $n */
    $plain = trim($n->nodeValue);
    if ($plain === '') continue;
    if (mb_strlen($plain, 'UTF-8') < $minChars) continue;

    // Translate only nodes containing letters or CJK
    if (!preg_match('/[A-Za-z\x{4E00}-\x{9FFF}]/u', $plain)) continue;

    $textNodes[] = $n;
  }

  $total = count($textNodes);
  if ($total === 0) return $html;

  // Translate in groups to reduce API calls
  for ($i = 0; $i < $total; $i += $groupSize) {
    $chunk = array_slice($textNodes, $i, $groupSize);

    $parts = [];
    foreach ($chunk as $tn) {
      $parts[] = trim($tn->nodeValue);
    }

    $joined = implode($DELIM, $parts);

    // Attempt one-shot translation
    $translatedJoined = ai_translate($joined, $apiKey, $model);
    usleep((int)round($sleep * 1_000_000));

    $translatedParts = explode($DELIM, $translatedJoined);

    // If segment count mismatch, fallback to per-node translation (safe)
    if (count($translatedParts) !== count($chunk)) {
      $translatedParts = [];
      foreach ($parts as $p) {
        $translatedParts[] = ai_translate($p, $apiKey, $model);
        usleep((int)round($sleep * 1_000_000));
      }
    }

    // Write back translations
    foreach ($chunk as $idx => $tn) {
      $newText = trim($translatedParts[$idx] ?? '');
      if ($newText !== '') {
        $tn->nodeValue = $newText;
      }
    }
  }

  // Extract inner HTML from wrapper
  $wrap = $dom->getElementById('__wrap__');
  if (!$wrap) return $html;

  $out = '';
  foreach ($wrap->childNodes as $child) {
    $out .= $dom->saveHTML($child);
  }

  return $out;
}

/* =========================
 *  MAIN
 * ========================= */

$done = load_done_nids($OUT_FILE);

$in = fopen($IN_FILE, 'r');
if (!$in) {
  fwrite(STDERR, "Cannot open input file: {$IN_FILE}\n");
  exit(1);
}

$out = fopen($OUT_FILE, 'a');
if (!$out) {
  fclose($in);
  fwrite(STDERR, "Cannot open output file for append: {$OUT_FILE}\n");
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
    // Translate title (plain text)
    $titleNew = ai_translate($titleSrc, $apiKey, $MODEL);
    usleep((int)round($SLEEP * 1_000_000));

    // Translate body (HTML text nodes only)
    $bodyNew = translate_html_text_nodes(
      $bodySrc,
      $apiKey,
      $MODEL,
      $DELIM,
      $SKIP_TAGS,
      $HTML_GROUP_SIZE,
      $HTML_MIN_CHARS,
      $SLEEP
    );
    usleep((int)round($SLEEP * 1_000_000));

    // Write output JSONL line
    $outRow = [
      'nid' => $nid,
      'lang' => $lang,
      'body_format' => $fmt,
      'title_new' => $titleNew,
      'body_new' => $bodyNew,
    ];

    fwrite($out, json_encode($outRow, JSON_UNESCAPED_UNICODE) . "\n");
    fflush($out);

    // Print nid one per line
    echo $nid . PHP_EOL;

    $done[$nid] = true;
  }
  catch (Throwable $e) {
    // Print errors to STDERR but continue
    fwrite(STDERR, "nid={$nid} FAILED: " . $e->getMessage() . "\n");
    // Do not mark done; rerun can resume
    continue;
  }
}

fclose($in);
fclose($out);

fwrite(STDERR, "✅ Done. Output: {$OUT_FILE}\n");
