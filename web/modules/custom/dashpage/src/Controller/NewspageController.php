<?php

namespace Drupal\dashpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
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
                $render_array = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'teaser');
                $output .= \Drupal::service('renderer')->renderRoot($render_array);
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

    if ($second_tid != 'all') {
      $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', 'article')
        ->condition('field_article_brand', $term_tid)
        ->sort('created', 'DESC')
        ->range(0, 5);
      $nids = $query->execute();
    }
    else {
      $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', 'article')
        ->condition('field_article_brand', $term_tid)
        ->condition('field_article_storymenu', $second_tid)
        ->sort('created', 'DESC')
        ->range(0, 5);
      $nids = $query->execute();
    }
    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple($nids);


    $output = $this->_getNewsPageHtml($nodes);

    return $output;
  }

  /**
   *
   */
  public function _getTermProductHtml($term_tid = NULL, $second_tid = NULL) {
    $output = NULL;

    if ($second_tid != 'all') {
      $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', 'article')
        ->condition('field_article_product', $term_tid)
        ->sort('created', 'DESC')
        ->range(0, 5);
      $nids = $query->execute();
    }
    else {
      $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', 'article')
        ->condition('field_article_product', $term_tid)
        ->condition('field_article_device', $second_tid)
        ->sort('created', 'DESC')
        ->range(0, 5);
      $nids = $query->execute();
    }
    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple($nids);

    $output = $this->_getNewsPageHtml($nodes);

    return $output;
  }

  /**
   *
   */
  public function _getTermPresscentreHtml($term_tid = NULL, $second_tid = NULL) {
    $output = NULL;

    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'article')
      ->sort('created', 'DESC')
      ->range(0, 10);
    $nids = $query->execute();
    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple($nids);

    $output = $this->_getNewsPageHtml($nodes);

    return $output;
  }

  /**
   *
   */
  public function _getTermSolutionHtml($term_tid = NULL, $second_tid = NULL) {
    $output = NULL;

    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'article')
      ->condition('field_article_solution', $term_tid)
      ->sort('created', 'DESC')
      ->range(0, 5);
    $nids = $query->execute();
    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple($nids);

    $output = $this->_getNewsPageHtml($nodes);

    return $output;
  }

}
