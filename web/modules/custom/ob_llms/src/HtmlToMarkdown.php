<?php

namespace Drupal\ob_llms;

use Drupal\Component\Utility\Html;

/**
 * Minimal HTML to Markdown converter based on DOM traversal.
 *
 * Keeps headings, paragraphs, lists, tables, links and basic emphasis.
 * Drops images, data URIs, scripts, styles and embedded media entirely.
 */
class HtmlToMarkdown {

  /**
   * Placeholder for significant spaces (list indentation, <pre> content).
   *
   * Every line is left-trimmed during cleanup; placeholders survive that and
   * are turned back into spaces afterwards.
   */
  protected const SPACE = "\x01";

  /**
   * Elements whose whole subtree is discarded.
   */
  protected const SKIP = [
    'img', 'picture', 'source', 'svg', 'script', 'style', 'noscript',
    'iframe', 'object', 'embed', 'video', 'audio', 'canvas', 'map',
    'form', 'input', 'button', 'select', 'textarea', 'head', 'meta', 'link',
  ];

  /**
   * Elements rendered as a separate block.
   */
  protected const BLOCK = [
    'p', 'div', 'section', 'article', 'header', 'footer', 'main', 'aside',
    'nav', 'figure', 'figcaption', 'center', 'address', 'dl', 'dt', 'dd',
    'details', 'summary',
  ];

  /**
   * Constructs the converter.
   *
   * @param string $baseUrl
   *   Absolute base URL used to resolve root-relative links.
   * @param int $headingOffset
   *   Levels added to body headings so they nest below the entry heading.
   */
  public function __construct(
    protected string $baseUrl,
    protected int $headingOffset = 2,
  ) {
    $this->baseUrl = rtrim($baseUrl, '/');
  }

  /**
   * Converts an HTML fragment to Markdown.
   */
  public function convert(?string $html): string {
    if ($html === NULL || trim($html) === '') {
      return '';
    }
    // Drop inline images and data URIs up front: bodies may carry megabytes
    // of base64 that would only bloat the DOM. The DOM pass removes any
    // leftovers anyway.
    $html = preg_replace('#<img\b[^>]*>#i', '', $html) ?? $html;
    $html = preg_replace('#data:[a-z]+/[a-z0-9.+-]+;base64,[a-z0-9+/=\s]*#i', '', $html) ?? $html;

    $body = Html::load($html)->getElementsByTagName('body')->item(0);
    if (!$body) {
      return '';
    }
    return str_replace(self::SPACE, ' ', $this->cleanup($this->children($body)));
  }

  /**
   * Renders all child nodes of an element.
   */
  protected function children(\DOMNode $node): string {
    $out = '';
    foreach ($node->childNodes as $child) {
      $out .= $this->node($child);
    }
    return $out;
  }

  /**
   * Renders a single node.
   */
  protected function node(\DOMNode $node): string {
    if ($node instanceof \DOMText) {
      return preg_replace('/\s+/u', ' ', str_replace("\u{00A0}", ' ', $node->nodeValue));
    }
    if (!$node instanceof \DOMElement) {
      return '';
    }

    $tag = strtolower($node->nodeName);
    if (in_array($tag, self::SKIP, TRUE)) {
      return '';
    }
    if (in_array($tag, self::BLOCK, TRUE)) {
      return $this->block(trim($this->children($node)));
    }

    switch ($tag) {
      case 'h1':
      case 'h2':
      case 'h3':
      case 'h4':
      case 'h5':
      case 'h6':
        $text = $this->oneLine($this->children($node));
        if ($text === '') {
          return '';
        }
        $level = min(6, (int) $tag[1] + $this->headingOffset);
        return $this->block(str_repeat('#', $level) . ' ' . $text);

      case 'br':
        return "\n";

      case 'hr':
        return $this->block('---');

      case 'strong':
      case 'b':
        return $this->wrap($this->children($node), '**');

      case 'em':
      case 'i':
        return $this->wrap($this->children($node), '*');

      case 'code':
        return $this->wrap($this->children($node), '`');

      case 'pre':
        $code = rtrim(str_replace(' ', self::SPACE, $node->textContent));
        return $code === '' ? '' : $this->block("```\n" . $code . "\n```");

      case 'a':
        return $this->link($node);

      case 'ul':
      case 'ol':
        return $this->listing($node, $tag === 'ol');

      case 'li':
        // A list item outside of any list.
        return $this->block('- ' . trim($this->children($node)));

      case 'table':
        return $this->table($node);

      case 'blockquote':
        $inner = $this->cleanup($this->children($node));
        if ($inner === '') {
          return '';
        }
        return $this->block(preg_replace('/^/m', '> ', $inner));

      default:
        // span, font, u, sup, sub, small, ... are transparent.
        return $this->children($node);
    }
  }

  /**
   * Surrounds a block with blank lines.
   */
  protected function block(string $text): string {
    return $text === '' ? '' : "\n\n" . $text . "\n\n";
  }

  /**
   * Wraps inline text in a marker, keeping surrounding whitespace outside.
   */
  protected function wrap(string $text, string $marker): string {
    $trimmed = trim($text);
    if ($trimmed === '') {
      return $text;
    }
    $lead = preg_match('/^\s/u', $text) ? ' ' : '';
    $trail = preg_match('/\s$/u', $text) ? ' ' : '';
    return $lead . $marker . $trimmed . $marker . $trail;
  }

  /**
   * Collapses rendered content to a single line.
   */
  protected function oneLine(string $text): string {
    return trim(preg_replace('/\s+/u', ' ', $text));
  }

  /**
   * Renders a link, dropping unusable targets but keeping the text.
   */
  protected function link(\DOMElement $node): string {
    $text = $this->oneLine($this->children($node));
    if ($text === '') {
      return '';
    }
    $url = $this->resolveUrl(trim($node->getAttribute('href')));
    if ($url === NULL) {
      return $text;
    }
    return '[' . str_replace(['[', ']'], ['\[', '\]'], $text) . '](' . $url . ')';
  }

  /**
   * Resolves an href to an absolute URL, or NULL when it is not useful.
   */
  protected function resolveUrl(string $href): ?string {
    if ($href === '' || $href[0] === '#' || preg_match('#^(data|javascript|blob):#i', $href)) {
      return NULL;
    }
    if (str_starts_with($href, '//')) {
      $href = 'http:' . $href;
    }
    elseif (str_starts_with($href, '/')) {
      $href = $this->baseUrl . $href;
    }
    elseif (!preg_match('#^[a-z][a-z0-9+.-]*:#i', $href)) {
      $href = $this->baseUrl . '/' . $href;
    }
    return str_replace([' ', '(', ')'], ['%20', '%28', '%29'], $href);
  }

  /**
   * Renders ul/ol, indenting nested content under each item marker.
   */
  protected function listing(\DOMElement $list, bool $ordered): string {
    $items = [];
    $n = (int) ($list->getAttribute('start') ?: 1);
    foreach ($list->childNodes as $child) {
      if (!$child instanceof \DOMElement) {
        continue;
      }
      $content = $this->cleanup($this->children($child));
      if ($content === '') {
        continue;
      }
      // Paragraphs inside an item would break the list; keep single breaks.
      $content = preg_replace('/\n{2,}/', "\n", $content);
      $marker = $ordered ? ($n++) . '. ' : '- ';
      $indent = str_repeat(self::SPACE, strlen($marker));
      $items[] = $marker . str_replace("\n", "\n" . $indent, $content);
    }
    return $items ? $this->block(implode("\n", $items)) : '';
  }

  /**
   * Renders a table as a GFM pipe table (first row is the header).
   */
  protected function table(\DOMElement $table): string {
    $rows = [];
    foreach ($this->tableRows($table) as $tr) {
      $cells = [];
      foreach ($tr->childNodes as $cell) {
        if ($cell instanceof \DOMElement && in_array(strtolower($cell->nodeName), ['td', 'th'], TRUE)) {
          $cells[] = str_replace('|', '\|', $this->oneLine(str_replace(self::SPACE, ' ', $this->children($cell))));
        }
      }
      if (implode('', $cells) !== '') {
        $rows[] = $cells;
      }
    }
    if (!$rows) {
      return '';
    }

    $width = max(array_map('count', $rows));
    $lines = [];
    foreach ($rows as $i => $cells) {
      $cells = array_pad($cells, $width, '');
      $lines[] = '| ' . implode(' | ', $cells) . ' |';
      if ($i === 0) {
        $lines[] = '|' . str_repeat(' --- |', $width);
      }
    }
    return $this->block(implode("\n", $lines));
  }

  /**
   * Collects the table's own rows, not those of nested tables.
   *
   * @return \DOMElement[]
   *   The tr elements, in document order.
   */
  protected function tableRows(\DOMElement $table): array {
    $rows = [];
    foreach ($table->childNodes as $child) {
      if (!$child instanceof \DOMElement) {
        continue;
      }
      $name = strtolower($child->nodeName);
      if ($name === 'tr') {
        $rows[] = $child;
      }
      elseif (in_array($name, ['thead', 'tbody', 'tfoot'], TRUE)) {
        foreach ($child->childNodes as $tr) {
          if ($tr instanceof \DOMElement && strtolower($tr->nodeName) === 'tr') {
            $rows[] = $tr;
          }
        }
      }
    }
    return $rows;
  }

  /**
   * Normalizes whitespace and blank lines; placeholders are kept.
   */
  protected function cleanup(string $text): string {
    $lines = array_map(fn($line) => trim($line, " \t"), explode("\n", $text));
    return trim(preg_replace("/\n{3,}/", "\n\n", implode("\n", $lines)), " \t\n");
  }

}
