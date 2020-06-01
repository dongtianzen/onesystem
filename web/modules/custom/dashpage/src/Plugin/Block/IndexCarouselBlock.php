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
    $build['sidebar_brand_block']['#markup'] = $this->_IndexCarouselHtml();

    return $build;
  }

  /**
   * {@inheritdoc}
   * @see https://getbootstrap.com/docs/3.3/javascript/#carousel
   */
  public function _IndexCarouselHtml() {
    $output = '';
    $output .= '<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">';
      // <!-- Indicators -->
      $output .= '<ol class="carousel-indicators">';
        $output .= '<li data-target="#carousel-example-generic" data-slide-to="0" class="active">';
        $output .= '</li>';
        $output .= '<li data-target="#carousel-example-generic" data-slide-to="1">';
        $output .= '</li>';
        $output .= '<li data-target="#carousel-example-generic" data-slide-to="2">';
        $output .= '</li>';
      $output .= '</ol>';

      // <!-- Wrapper for slides -->
      $image_path_1 = drupal_get_path('module', 'dashpage') . '/image/47.png';
      $image_path_1 = drupal_get_path('module', 'dashpage') . '/image/77.png';
      $image_path_1 = drupal_get_path('module', 'dashpage') . '/image/79.png';
      $output .= '<div class="carousel-inner" role="listbox">';
        $output .= '<div class="item active">';
          $output .= '<img src=" ' . . '" alt="...">';
          $output .= '<div class="carousel-caption">';
              $output .= '...';
          $output .= '</div>';
        $output .= '</div>';
        $output .= '<div class="item">';
          $output .= '<img src="" alt="...">';
          $output .= '<div class="carousel-caption">';
            $output .= '...';
          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';

      // <!-- Controls -->
      $output .= '<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">';
        $output .= '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true">';
        $output .= '</span>';
        $output .= '<span class="sr-only">';
          $output .= 'Previous';
        $output .= '</span>';
      $output .= '</a>';

      $output .= '<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">';
        $output .= '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true">';
        $output .= '</span>';
        $output .= '<span class="sr-only">';
          $output .= 'Next';
        $output .= '</span>';
      $output .= '</a>';
    $output .= '</div>';
    return $output;
  }

}
