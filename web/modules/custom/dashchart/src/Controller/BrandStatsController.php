<?php

namespace Drupal\dashchart\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\TermInterface;

class BrandStatsController extends ControllerBase {

  public function page(): array {

    // brand vocabulary machine name
    $vocab = 'brand';

    $term_storage = $this->entityTypeManager()->getStorage('taxonomy_term');
    $terms = $term_storage->loadTree($vocab, 0, NULL, TRUE);

    $labels = [];
    $counts = [];

    foreach ($terms as $term) {
      if (!$term instanceof TermInterface) {
        continue;
      }

      $labels[] = $term->label();
      $counts[] = $this->countNodesByBrand($term->id());
    }

    // 按数量排序（大到小）
    $combined = array_map(null, $labels, $counts);
    usort($combined, fn($a, $b) => $b[1] <=> $a[1]);

    $labels = array_column($combined, 0);
    $counts = array_column($combined, 1);

    return [
      '#theme' => 'dashchart_brand_stats',
      '#attached' => [
        'library' => ['dashchart/brand_chart'],
        'drupalSettings' => [
          'dashchart' => [
            'brandStats' => [
              'labels' => $labels,
              'counts' => $counts,
            ],
          ],
        ],
      ],
      '#cache' => [
        'contexts' => ['url.path', 'languages:language_interface'],
      ],
    ];
  }

  private function countNodesByBrand(int $tid): int {
    $field_name = 'field_article_brand';

    $query = $this->entityTypeManager()->getStorage('node')->getQuery();
    $query->accessCheck(TRUE);
    $query->condition('status', 1);
    $query->condition($field_name . '.target_id', $tid);

    // 如果只统计某个内容类型，例如 product：
    // $query->condition('type', 'product');

    return (int) $query->count()->execute();
  }

}
