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

    $build = [
      '#theme' => 'index_carousel_block',
      '#carouselrows' => [
        [
          'image_src' => 'themes/custom/wanbo/images/slider/live streaming_v1.png',
          'title' => 'Carousel Item 1',
          'content' => 'Carousel Item 1 Content',
        ],
        [
          'image_src' => 'themes/custom/wanbo/images/slider/live streaming_v2.png',
          'title' => 'Carousel Item 2',
          'content' => 'This is the content for Carousel Item 2',
        ],
        [
          'image_src' => 'themes/custom/wanbo/images/slider/live streaming_v3.jpg',
          'title' => 'Carousel Item 3',
          'content' => 'This is the content for Carousel Item 3',
        ],
      ],
      '#content' => $this->t('This is a custom block.'),
    ];

    return $build;
  }

}
