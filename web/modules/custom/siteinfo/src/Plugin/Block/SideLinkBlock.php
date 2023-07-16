<?php

namespace Drupal\siteinfo\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

use Drupal\Core\Menu\MenuTreeParameters;

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
      $output = $this->getLinksSpecificParentItem('siteinfo.link.service.menu');
    }
    else if ($current_path == '/node/429') {

    }



    return $output;
  }

  /**
   *
   */
  public function getLinksFromTerms($terms = []) {
    $output = NULL;

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


  /**
   * Get all menu items of a specific menu.
   */
  function getLinksSpecificParentItem($tree_key = NULL) {
    $output = NULL;

    $parameters = new MenuTreeParameters();
    $menu_name = 'main';

    // Optionally limit to enabled items.
    $parameters->onlyEnabledLinks();

    // Optionally set active trail.
    $menu_active_trail = \Drupal::service('menu.active_trail')->getActiveTrailIds($menu_name);
    $parameters->setActiveTrail($menu_active_trail);

    // Load the tree.
    $menu_tree = \Drupal::menuTree()->load($menu_name, $parameters);

    if (isset($menu_tree[$tree_key])) {
      $subtree = $menu_tree[$tree_key]->subtree;

      foreach ($subtree as $key => $menu_link) {
        $menu_link_data = $menu_link->link;
        $output .= '<div class="side-link-block-wrapper">';
          $output .= \Drupal::l($menu_link_data->getTitle(), $menu_link_data->getUrlObject());
        $output .= '</div>';
      }
    }

    return $output;
  }

}
