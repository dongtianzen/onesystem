<?php

namespace Drupal\dashpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */
class NewspageController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   * dpm($request->getpathInfo());
   */
  public function newspageStandardTerm($vid_name, $term_tid = NULL, $second_tid = NULL, Request $request) {
    $markup = '';
    if ($vid_name == 'brand') {
      $markup = $this->_getTermBrandHtml($term_tid, $second_tid);
    }
    else if ($vid_name == 'product') {
      $markup = $this->_getTermProductHtml($term_tid, $second_tid);
    }
    else if ($vid_name == 'solution') {
      $markup = $this->_getTermSolutionHtml($term_tid, $second_tid);
    }

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
    );

    return $build;
  }

  /**
   * @return string
   */
  public function _getNewsPageHtml($nodes = array()) {
    $output = NULL;

    if ($nodes && is_array($nodes)) {
      foreach ($nodes as $node) {

        $output .= '<div class="newspage-row">';
          $output .= '<article class="contextual-region node node--type-article node--promoted node--view-mode-teaser clearfix">';
          $output .= '<div class="node__container">';
            $output .= '<div class="node__main-content clearfix">';

              $output .= '<div class="node__main-content-section">';
                $entity_view = entity_view($node, $view_mode = 'teaser');
                $output .= render($entity_view);
              $output .= '</div>';

            $output .= '</div>';

          $output .= '</div>';
          $output .= '</article>';
        $output .= '</div>';
      }
    }

    return $output;
  }

  /**
   *
   */
  public function _getTermBrandHtml($term_tid = NULL, $second_tid = NULL) {
    $output = NULL;

    $query = \Drupal::service('flexinfo.querynode.service')
      ->queryNidsByBundle('article');
    $group = \Drupal::service('flexinfo.querynode.service')
      ->groupStandardByFieldValue($query, $field_name = 'field_article_brand', $term_tid);
    $query->condition($group);

    // all is default value which defined on routing.yml
    if ($second_tid != 'all') {
      $group = \Drupal::service('flexinfo.querynode.service')
        ->groupStandardByFieldValue($query, $field_name = 'field_article_storymenu', $second_tid);
      $query->condition($group);
    }

    $query->sort('created', 'DESC');
    $query->pager(5);

    $nids = \Drupal::service('flexinfo.querynode.service')
      ->runQueryWithGroup($query);
    $nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($nids);

    $output = $this->_getNewsPageHtml($nodes);

    return $output;
  }

  /**
   *
   */
  public function _getTermProductHtml($term_tid = NULL, $second_tid = NULL) {
    $output = NULL;

    $query = \Drupal::service('flexinfo.querynode.service')
      ->queryNidsByBundle('article');
    $group = \Drupal::service('flexinfo.querynode.service')
      ->groupStandardByFieldValue($query, $field_name = 'field_article_product', $term_tid);
    $query->condition($group);

    // all is default value which defined on routing.yml
    if ($second_tid != 'all') {
      $group = \Drupal::service('flexinfo.querynode.service')
        ->groupStandardByFieldValue($query, $field_name = 'field_article_device', $second_tid);
      $query->condition($group);
    }

    $query->sort('created', 'DESC');
    $query->pager(5);

    $nids = \Drupal::service('flexinfo.querynode.service')
      ->runQueryWithGroup($query);
    $nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($nids);

    $output = $this->_getNewsPageHtml($nodes);

    return $output;
  }

  /**
   *
   */
  public function _getTermPresscentreHtml($term_tid = NULL, $second_tid = NULL) {
    $output = NULL;

    $query = \Drupal::service('flexinfo.querynode.service')
      ->queryNidsByBundle('article');
    $query->sort('created', 'DESC');
    $query->pager(10);

    $nids = \Drupal::service('flexinfo.querynode.service')
      ->runQueryWithGroup($query);
    $nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($nids);

    $output = $this->_getNewsPageHtml($nodes);

    return $output;
  }

  /**
   *
   */
  public function _getTermSolutionHtml($term_tid = NULL, $second_tid = NULL) {
    $output = NULL;

    $query = \Drupal::service('flexinfo.querynode.service')
      ->queryNidsByBundle('article');
    $group = \Drupal::service('flexinfo.querynode.service')
      ->groupStandardByFieldValue($query, $field_name = 'field_article_solution', $term_tid);
    $query->condition($group);
    $query->sort('created', 'DESC');
    $query->pager(6);

    $nids = \Drupal::service('flexinfo.querynode.service')
      ->runQueryWithGroup($query);
    $nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($nids);

    $output = $this->_getNewsPageHtml($nodes);

    return $output;
  }

}
