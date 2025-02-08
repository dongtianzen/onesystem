<?php

namespace Drupal\dashpage\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Custom HTML Block.
 *
 * @Block(
 *   id = "feature_first_block",
 *   admin_label = @Translation("Feature First Block"),
 *   category = @Translation("Custom")
 * )
 */
class FeatureFirstBlock extends BlockBase {
  
  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#theme' => 'feature_first_block',
      '#industries' => [
        ['title' => 'Education', 'icon' => 'education-icon.svg'],
        ['title' => 'E-Commerce', 'icon' => 'ecommerce-icon.svg'],
        ['title' => 'Healthcare', 'icon' => 'healthcare-icon.svg'],
        ['title' => 'Finance', 'icon' => 'finance-icon.svg'],
        ['title' => 'Automotive', 'icon' => 'automotive-icon.svg'],
        ['title' => 'Software', 'icon' => 'software-icon.svg'],
      ],
      '#content' => $this->t('This is a custom block.'),
    ];

    return $build;
  }

}
