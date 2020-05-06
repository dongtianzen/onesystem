<?php

namespace Drupal\dashpage\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'SidebarBrandBlock' block.
 *
 * @Block(
 *  id = "sidebar_brand_block",
 *  admin_label = @Translation("Sidebar brand block"),
 * )
 */
class SidebarBrandBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'sidebar_brand_block';
     $build['sidebar_brand_block']['#markup'] = 'Implement SidebarBrandBlock.';

    return $build;
  }

}
