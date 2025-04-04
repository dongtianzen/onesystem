<?php

namespace Drupal\dashpage\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\UncacheableDependencyTrait;
use Drupal\Core\Link;
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

    if ($path_args[1] == 'newspage' && $path_args[2] == 'term') {

      $tid = $path_args[4];
      $term = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->load($tid);

      if ($term) {
        $output .= '<nav role="navigation" aria-labelledby="block-showcase-lite-account-menu-menu" id="block-showcase-lite-account-menu" class="clearfix block block-menu navigation menu--account">';
          $output .= '<h2>';
            $output .= $term->getName();
          $output .= '</h2>';

          if ($path_args[3] == 'brand') {
            $output .= $this->_SidebarMenuLinkBrand($term);
          }
          else if ($path_args[3] == 'product') {
            $output .= $this->_SidebarMenuLinkProduct($term);
          }

          $output .= '<ul class="clearfix menu">';
            $output .= '<li class="menu-item">';
              $link_path = '/dashboard/category/brand';
              $output .= Link::fromTextAndUrl('返回品牌故事', Url::fromUserInput($link_path))->toString();
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
  public function _SidebarMenuLinkBrand($term) {
    $output = '';

    if ($term) {
      $entitys = \Drupal::service('flexinfo.field.service')
        ->getFieldAllTargetIdsEntitys($term, 'field_brand_storymenu');

      if ($entitys) {
        foreach ($entitys as $key => $row) {
          // $link_path = '/newspage/term/brand/' . $term->id() . '/' . $row->id();
          $link_path = 'taxonomy/term/' . $term->id();

          $output .= '<h2 class="height-38">';
            $output .= '<span class="margin-left-12 float-left translateX-hover translateX-5">';
              $output .= Link::fromTextAndUrl($row->getName(), Url::fromUserInput($link_path))->toString();
            $output .= '</span>';
          $output .= '</h2>';
        }
      }
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function _SidebarMenuLinkProduct($term) {
    $output = '';

    if ($term) {
      $entitys = \Drupal::service('flexinfo.queryterm.service')
        ->wrapperTermEntitysByField('device', 'field_device_product', $term->id());

      if ($entitys) {
        foreach ($entitys as $key => $row) {
          // $link_path = '/newspage/term/product/' . $term->id() . '/' . $row->id();
          $link_path = 'taxonomy/term/' . $term->id();

          $output .= '<h2 class="height-38">';
            $output .= '<span class="margin-left-12 float-left translateX-hover translateX-5">';
              $output .= Link::fromTextAndUrl($row->getName(), Url::fromUserInput($link_path))->toString();
            $output .= '</span>';
          $output .= '</h2>';
        }
      }
    }

    return $output;
  }

}
