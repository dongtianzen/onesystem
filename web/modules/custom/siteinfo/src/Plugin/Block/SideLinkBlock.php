<?php

namespace Drupal\siteinfo\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides a 'Side Link' Block.
 *
 * @Block(
 *   id = "side_link_block",
 *   admin_label = @Translation("Siteinfo Side Link Block"),
 *   category = @Translation("Side Link Block"),
 * )
 */
class SideLinkBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = array();
    $build['#markup'] = $this->switchLinkContent();
    $build['#attached']['library'][] = 'siteinfo/side.link.style';

    return $build;
  }

  /**
   *
   */
  public function switchLinkContent() {
    $output = NULL;

    $current_path = \Drupal::service('path.current')->getPath();

    $terms = [];
    if ($current_path == '/dashpage/hello/presscentre') {
      $terms = $this->getFullTermsFromVidName('news');
    }
    else if ($current_path == '/dashpage/hello/product') {
      $terms = $this->getFullTermsFromVidName('brand');
    }
    else if ($current_path == '/dashpage/hello/solution') {
      $terms = $this->getFullTermsFromVidName('solution');
    }
    else if ($current_path == '/dashpage/hello/service') {

    }
    else if ($current_path == '/node/429') {

    }


    // dump($current_path);
    // dump($terms);
    if ($terms && count($terms) > 0) {
      foreach ($terms as $key => $term) {
        $output .= '<div class="side-link-block-wrapper">';
          $output .= \Drupal::l($term->getName(), Url::fromUserInput('/taxonomy/term/' . $term->id()));
        $output .= '</div>';
      }
    }

    return $output;
  }

  /**
   *
   */
  public function linkContent() {
    $output = NULL;

    $term_names = $this->linkTermBrand();
    foreach ($term_names as $key => $term_name) {
      $terms = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties(['name' => $term_name]);
      if ($terms) {
        $term = reset($terms);
        if ($term) {
          $output .= '<div class="side-link-block-wrapper">';
            $output .= \Drupal::l($term_name, Url::fromUserInput('/taxonomy/term/' . $term->id()));
          $output .= '</div>';
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function linkTermBrand() {
    $output = array(
      'AppearTV',
      'Elemental',
      'Harmonic',
      'HARRIS',
      'LiveU',
      'PBI',
      'Peplink',
      'PHABRIX',
      'Tektronix',
    );

    return $output;
  }

  /**
   * @return array, terms entity
   \Drupal::service('flexinfo.term.service')->getFullTermsFromVidName($vid);
   */
  public function getFullTermsFromVidName($vid = NULL) {
    $tids = \Drupal::service('flexinfo.term.service')
      ->getTidsFromVidName($vid);
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadMultiple($tids);

    return $terms;
  }

}
