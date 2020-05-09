<?php

namespace Drupal\dashpage\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\UncacheableDependencyTrait;

/**
 * Provides a 'SidebarBrandBlock' block.
 *
 * @Block(
 *  id = "sidebar_brand_block",
 *  admin_label = @Translation("Sidebar brand block"),
 * )
 */
class SidebarBrandBlock extends BlockBase {

  use UncacheableDependencyTrait;

  /**
   * {@inheritdoc}
    $build['#attached'] = array(
      'library' => array('dashpage/dashpage-page-style'),
    );
   */
  public function build() {
    $build = [];

    // Do NOT cache a page with this block on it.
    \Drupal::service('page_cache_kill_switch')->trigger();

    $build['#theme'] = 'sidebar_brand_block';
    $build['sidebar_brand_block']['#markup'] = $this->_SidebarBrandMenu();

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function _SidebarBrandMenu() {
    $output = '';

    $tid = 27;

    $output .= '<nav role="navigation" aria-labelledby="block-showcase-lite-account-menu-menu" id="block-showcase-lite-account-menu" class="clearfix block block-menu navigation menu--account">';
      $output .= '<h2>';
        $output .= \Drupal::service('flexinfo.term.service')->getNameByTid($tid);
      $output .= '</h2>';
      $output .= '<ul class="clearfix menu">';
        $output .= '<li class="menu-item">';
        $output .= '<a>';
          $output .= '其它事情';
        $output .= '</a>';
        $output .= '</li>';
      $output .= '</ul>';
    $output .= '</nav>';

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function _SidebarBrandMenuLink($tid) {
    $output = '';
    $output .= '<h2>';
      $output .= '<span class="margin-left-12">';
        $output .= '现场设备';
      $output .= '</span>';
    $output .= '</h2>';
    $output .= '<h2>';
      $output .= '<span class="margin-left-12">';
        $output .= '接收终端';
      $output .= '</span>';
    $output .= '</h2>';
    $output .= '<h2>';
      $output .= '<span class="margin-left-12">';
        $output .= '车载产品';
      $output .= '</span>';
    $output .= '</h2>';
    $output .= '<h2>';
      $output .= '<span class="margin-left-12">';
        $output .= '增值服务';
      $output .= '</span>';
    $output .= '</h2>';

    return $output;
  }

}
