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
    $markup = $this->linkContent();

    return array(
      '#markup' => $markup,
    );
  }

  /**
   *
   */
  public function linkContent() {
    $output = NULL;

    $term_names = $this->linkTermBrand();
    foreach ($term_names as $key => $term_name) {
      $term = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties(['name' => $term_name]);
      if ($term) {
        $url = Url::fromUserInput('/taxonomy/term/' . $term->id());

        $output .= '<div>';
          $output .= \Drupal::l($term_name, Url::fromUserInput($uri));
        $output .= '</div>';
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
