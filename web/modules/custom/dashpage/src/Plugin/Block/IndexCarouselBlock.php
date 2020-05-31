<?php

namespace Drupal\dashpage\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\UncacheableDependencyTrait;
use Drupal\Core\Url;

/**
 * Provides a 'IndexCarouselBlock' block.
 *
 * @Block(
 *  id = "index_carousel_block",
 *  admin_label = @Translation("Index Carousel Block"),
 * )
 */
class IndexCarouselBlock extends BlockBase {

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
    $output = 'IndexCarouselBlock';

    return $output;
  }

}
