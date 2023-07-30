<?php

namespace Drupal\siteinfo\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
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
   * This function switches link content based on the current path.
   *
   * @return string|null
   *   Returns the generated output for the links or NULL if no matching path is found.
   */
  public function switchLinkContent() {
    $output = NULL;

    $current_path = \Drupal::service('path.current')->getPath();

    $terms = [];
    if ($current_path == '/dashpage/hello/presscentre') {
      $output = $this->getLinksSpecificParentItem('siteinfo.link.news.menu');
    }
    else if ($current_path == '/dashpage/hello/product') {
      $output = $this->getLinksSpecificParentItem('siteinfo.link.brand.menu');
    }
    else if ($current_path == '/dashpage/hello/solution') {
      $output = $this->getLinksSpecificParentItem('siteinfo.link.solution.menu');
    }
    else if ($current_path == '/dashpage/hello/technologyhub') {
      $output = $this->getLinksSpecificParentItem('siteinfo.link.technologyhub.menu');
    }
    else if ($current_path == '/dashpage/hello/service') {
      $output = $this->getLinksSpecificParentItem('siteinfo.link.service.menu');
    }
    else if ($current_path == '/node/429') {
      $output = $this->getLinksSpecificParentItem('siteinfo.link.aboutus.menu');

    }
    return $output;
  }

  /**
   * Generate a string of links from an array of taxonomy term entities.
   *
   * @param \Drupal\taxonomy\Entity\Term[] $terms
   *   An array of taxonomy term entities.
   *
   * @return string
   *   Returns the generated output as a string containing links.
   */
  public function getLinksFromTerms($terms = []) {
    $output = NULL;

    if ($terms && count($terms) > 0) {
      foreach ($terms as $key => $term) {
        $output .= '<div class="side-link-block-wrapper">';
          $output .= Link::fromTextAndUrl($term->getName(), Url::fromUserInput('/taxonomy/term/' . $term->id()))->toString();
        $output .= '</div>';
      }
    }

    return $output;
  }

  /**
   * Get an array of full taxonomy term entities based on a vocabulary ID.
   *
   * @param string|null $vid
   *   The vocabulary ID from which to load the terms.
   *
   * @return \Drupal\taxonomy\Entity\Term[]
   *   Returns an array of taxonomy term entities.
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
   * Get links from a specific menu tree with a specific parent item.
   *
   * @param string|null $tree_key
   *   The key for the menu tree item that represents the parent.
   *
   * @return string
   *   Returns the generated output as a string containing links.
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
          $output .= Link::fromTextAndUrl($menu_link_data->getTitle(), $menu_link_data->getUrlObject())->toString();
        $output .= '</div>';
      }
    }

    return $output;
  }

}
