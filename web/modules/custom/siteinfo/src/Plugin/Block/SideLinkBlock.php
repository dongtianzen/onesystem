<?php

namespace Drupal\siteinfo\Plugin\Block;

use Drupal\Core\Block\BlockBase;

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
    $term_names = $this->linkTermBrand();
    foreach ($term_names as $key => $term_name) {
      $term = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties(['name' => $term_name]);
      if ($term) {

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
