<?php

namespace Drupal\dashpage\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'Custom Feature Block' block.
 *
 * @Block(
 *   id = "feature_third_block",
 *   admin_label = @Translation("Feature Third Block"),
 *   category = @Translation("Custom")
 * )
 */
class FeatureThirdBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $vid = 'index_block_third';
    $icons = $this->getSvgIcons();

    // 当前界面语言（切换语言时 term 会跟着切）
    $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();

    // 返回完整 Term 实体（按 weight + name 排序）
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vid, 0, NULL, TRUE);

    $features = [];
    $cache_tags = ["taxonomy_term_list:$vid"];

    /** @var \Drupal\taxonomy\TermInterface $term */
    foreach ($terms as $key => $term) {
      // cache tags：term 更新时自动失效
      $cache_tags = Cache::mergeTags($cache_tags, $term->getCacheTags());

      // 多语言：切到对应翻译
      if ($term->isTranslatable() && $term->hasTranslation($langcode)) {
        $term = $term->getTranslation($langcode);
      }

      $title = $term->label();
      $desc_item = $term->get('description')->first();
      $description = [];

      if ($desc_item && !$desc_item->isEmpty()) {
        $description = [
          '#type' => 'processed_text',
          '#text' => $desc_item->value,
          '#format' => $desc_item->format,
        ];
      }

      $icon = $icons[$key];
      $key++;

      $features[] = [
        'icon' => $icon,
        'title' => $title,
        'description' => $description,
      ];
    }

    return [
      '#theme' => 'feature_third_block',
      '#features' => $features,
      '#news' => [],
      '#cache' => [
        'contexts' => [
          'languages:language_interface',
          'languages:language_content',
          'url.path',
          'url.query_args',
        ],
        'tags' => $cache_tags,
        'max-age' => 3600,
      ],
    ];
  }

  /**
   * 这里放你那 6 个 SVG（24x24 那套）
   */
  private function getSvgIcons(): array {
    return [
      // 0
      '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
      // 1
      '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="16"></line></svg>',
      // 2
      '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
      // 3
      '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>',
      // 4
      '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
      // 5
      '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
    ];
  }

}
