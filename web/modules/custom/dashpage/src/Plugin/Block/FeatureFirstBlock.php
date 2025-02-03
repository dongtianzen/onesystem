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
    return [
      '#markup' => '<div class="custom-html-block">
                      <h2>Welcome to My Custom Block</h2>
                      <p>This is a custom block with HTML content in Drupal 10.</p>
                    </div>',
      '#allowed_tags' => ['div', 'h2', 'p'],
    ];
  }

}
