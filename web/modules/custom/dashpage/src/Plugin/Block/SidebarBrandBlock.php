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
    $build['sidebar_brand_block']['#markup'] = $this->_SidebarBrandMenu();

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function _SidebarBrandMenu() {
    $output = '';
    $output .= '<nav role="navigation" aria-labelledby="block-showcase-lite-account-menu-menu" id="block-showcase-lite-account-menu" class="clearfix block block-menu navigation menu--account">';
      $output .= '<h2 id="block-showcase-lite-account-menu-menu">';
      $output .= 'User account menu';
      $output .= '</h2>';
      $output .= '<ul class="clearfix menu">';
        $output .= '<li class="menu-item">';
          $output .= '<a href="/showcase-lite/site/user/login" data-drupal-link-system-path="user/login" class="is-active">';
            $output .= 'Log in';
          $output .= '</a>';
        $output .= '</li>';
      $output .= '</ul>';
    $output .= '</nav>';

    return $output;
  }

}
