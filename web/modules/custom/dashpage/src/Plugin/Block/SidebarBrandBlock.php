<?php

namespace Drupal\dashpage\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\UncacheableDependencyTrait;
use Drupal\Core\Url;

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

    $path_args = \Drupal::service('flexinfo.setting.service')
      ->getCurrentPathArgs();

    if ($path_args[1] == 'newspage' && $path_args[3] == 'brand') {
      $tid = $path_args[4];
      $term = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->load($tid);

      if ($term) {
        $output .= '<nav role="navigation" aria-labelledby="block-showcase-lite-account-menu-menu" id="block-showcase-lite-account-menu" class="clearfix block block-menu navigation menu--account">';
          $output .= '<h2>';
            $output .= $term->getName();
          $output .= '</h2>';

          $output .= $this->_SidebarBrandMenuLink($term);

          $output .= '<ul class="clearfix menu">';
            $output .= '<li class="menu-item">';
            $output .= '<a>';
              $output .= '其它事情';
            $output .= '</a>';
            $output .= '</li>';
          $output .= '</ul>';
        $output .= '</nav>';

      }
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function _SidebarBrandMenuLink($term) {
    $output = '';

    if ($term) {
      $entitys = \Drupal::service('flexinfo.field.service')
        ->getFieldAllTargetIdsEntitys($term, 'field_brand_storymenu');

      if ($entitys) {
        foreach ($entitys as $key => $row) {
          $link_path = '/newspage/term/brand/' . $term->id() . '/' . $row->id();

          $output .= '<h2 class="height-38">';
            $output .= '<span class="margin-left-12 float-left translateX-hover translateX-5">';
              $output .= \Drupal::l($row->getName(), Url::fromUserInput($link_path));;
            $output .= '</span>';
          $output .= '</h2>';
        }
      }
    }

    return $output;
  }

}
