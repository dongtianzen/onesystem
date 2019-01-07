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
    $build['#markup'] = $this->linkContent();
    $build['#attached']['library'][] = 'siteinfo/site.link.style';

    return $build;
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

}
