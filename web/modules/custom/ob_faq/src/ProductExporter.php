<?php

declare(strict_types=1);

namespace Drupal\ob_faq;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\node\NodeInterface;

/**
 * Exports product/solution article text and existing FAQs as plain data.
 *
 * Used to draft FAQs outside Drupal; the result is re-imported with
 * FaqImporter.
 */
final class ProductExporter {

  private const LANGS = ['zh' => 'zh-hans', 'en' => 'en'];
  private const MAX_TEXT = 15000;

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * Counts for the export page.
   */
  public function counts(): array {
    return [
      'pages' => (int) $this->articleQuery(FALSE)->count()->execute(),
      'with_device' => (int) $this->articleQuery(TRUE)->count()->execute(),
      'faqs' => (int) $this->entityTypeManager->getStorage('node')->getQuery()
        ->accessCheck(FALSE)->condition('type', 'faq')->count()->execute(),
    ];
  }

  /**
   * Builds the export data.
   */
  public function export(bool $withDevice): array {
    $storage = $this->entityTypeManager->getStorage('node');
    $nids = array_values($this->articleQuery($withDevice)->execute());
    $pages = [];
    foreach (array_chunk($nids, 50) as $chunk) {
      foreach ($storage->loadMultiple($chunk) as $node) {
        $pages[] = $this->exportPage($node);
      }
      $storage->resetCache($chunk);
    }
    return [
      'generated' => date('c'),
      'filter' => $withDevice ? 'product/solution articles with a device term' : 'all product/solution articles',
      'page_count' => count($pages),
      'pages' => $pages,
      'existing_faqs' => $this->exportFaqs(),
    ];
  }

  /**
   * Published articles that have a product or solution term.
   */
  private function articleQuery(bool $withDevice): QueryInterface {
    $query = $this->entityTypeManager->getStorage('node')->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'article')
      ->condition('status', 1);
    $or = $query->orConditionGroup()
      ->exists('field_article_product')
      ->exists('field_article_solution');
    $query->condition($or);
    if ($withDevice) {
      $query->exists('field_article_device');
    }
    return $query->sort('nid');
  }

  /**
   * One article: URLs, titles, text and all taxonomy terms, zh + en.
   */
  private function exportPage(NodeInterface $node): array {
    $out = ['nid' => (int) $node->id()];
    foreach (self::LANGS as $key => $langcode) {
      if (!$node->hasTranslation($langcode)) {
        continue;
      }
      $translation = $node->getTranslation($langcode);
      if (!$translation->isPublished()) {
        continue;
      }
      $text = $this->plain((string) ($translation->get('body')->value ?? ''));
      $out[$key] = [
        'url' => $translation->toUrl('canonical', ['absolute' => TRUE])->toString(TRUE)->getGeneratedUrl(),
        'title' => $translation->label(),
        'text' => mb_substr($text, 0, self::MAX_TEXT),
      ];
    }
    $out['terms'] = $this->exportTerms($node);
    return $out;
  }

  /**
   * All taxonomy reference fields on the node: field name => terms.
   */
  private function exportTerms(NodeInterface $node): array {
    $terms = [];
    foreach ($node->getFieldDefinitions() as $name => $definition) {
      if ($definition->getType() !== 'entity_reference' || $definition->getSetting('target_type') !== 'taxonomy_term') {
        continue;
      }
      foreach ($node->get($name)->referencedEntities() as $term) {
        $row = ['tid' => (int) $term->id(), 'vocabulary' => $term->bundle()];
        foreach (self::LANGS as $key => $langcode) {
          if ($term->hasTranslation($langcode)) {
            $row[$key] = $term->getTranslation($langcode)->label();
          }
        }
        $terms[$name][] = $row;
      }
    }
    return $terms;
  }

  /**
   * Existing FAQs (any status), to avoid drafting duplicates.
   */
  private function exportFaqs(): array {
    $storage = $this->entityTypeManager->getStorage('node');
    $nids = $storage->getQuery()->accessCheck(FALSE)->condition('type', 'faq')->sort('nid')->execute();
    $faqs = [];
    foreach ($storage->loadMultiple($nids) as $faq) {
      $row = ['nid' => (int) $faq->id()];
      foreach (self::LANGS as $key => $langcode) {
        if ($faq->hasTranslation($langcode)) {
          $translation = $faq->getTranslation($langcode);
          $row[$key] = ['question' => $translation->label(), 'published' => $translation->isPublished()];
        }
      }
      $row['device_tids'] = array_map('intval', array_column($faq->get('field_faq_device')->getValue(), 'target_id'));
      $faqs[] = $row;
    }
    return $faqs;
  }

  /**
   * HTML to readable plain text, keeping table cells and line breaks.
   */
  private function plain(string $html): string {
    $html = (string) preg_replace(
      ['#<(script|style)\b.*?</\1>#is', '#<br\s*/?>#i', '#</(p|div|li|tr|h[1-6])>#i', '#</t[dh]>#i'],
      ['', "\n", "\n", ' | '],
      $html
    );
    $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = (string) preg_replace('/[ \t\x{00A0}]+/u', ' ', $text);
    $text = (string) preg_replace('/\n\s*\n+/u', "\n", $text);
    return trim($text);
  }

}
