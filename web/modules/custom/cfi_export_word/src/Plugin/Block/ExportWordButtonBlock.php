<?php
/**
 * @file
 * Contains \Drupal\cfi_export_word\Plugin\Block\ExportWordButtonBlock.
 */

namespace Drupal\cfi_export_word\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides a 'Export Word Button' Block.
 *
 * @Block(
 *   id = "export_word_button_block",
 *   admin_label = @Translation("Export Word Button"),
 *   category = @Translation("CFI Export Word"),
 * )
 */
class ExportWordButtonBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = array();

    $node = \Drupal::routeMatch()->getParameter('node');

    if ($node && ($node instanceof \Drupal\node\NodeInterface)) {
      if ($node->getType() == 'facility') {
        $build = array(
          '#title' => $this->t('Export Word'),
          '#type' => 'link',
          '#url' => \Drupal\Core\Url::fromUserInput('/export-word/content/page/' . $node->id()),
          '#prefix' => '<div class="export-word-button-wrapper action-buttons">',
          '#suffix' => '</div>',
        );
      }
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
