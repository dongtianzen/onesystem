<?php

namespace Drupal\dashpage\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

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
    $build = [
      '#theme' => 'feature_third_block',
      '#content' => $this->t('This is a custom block.'),
    ];

    return $build;
  }
}
