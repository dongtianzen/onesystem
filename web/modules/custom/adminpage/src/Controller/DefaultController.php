<?php

namespace Drupal\adminpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Class DefaultController.
 */
class DefaultController extends ControllerBase {

  /**
   * @return string
   *   Return Hello string.
   */
  public function adminpageStandard($name) {
    $markup = $this->_guidePage();

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
      '#attached' => array(
        'library' => array(
        ),
      ),
    );

    return $build;
  }

  /**
   * @return string
   *   Return Hello string.
   */
  public function _guidePage() {
    $output = '';
    $output .= '<div class="adminpage-content-wrapper">';

      $output .= '<div class="alert alert-light" role="alert">';
        $output .= '<div class="row padding-0">';
          $output .= '<div class="col-md-3">';
            $output .= '<h5 class="animated-hover">';
              $output .= 'Content';
            $output .= '</h5>';
          $output .= '</div>';
          $output .= '<div class="col-md-5">';
            $output .= '<h5>';
              $output .= '<i class="fa fa-edit"></i>';
              $internal_url = \Drupal\Core\Url::fromUserInput('/adminpage/views/content/node');
              $output .= Link::fromTextAndUrl('管理内容', $internal_url)->toString();
            $output .= '</h5>';
          $output .= '</div>';
          $output .= '<div class="col-md-4">';
            $output .= '<h5>';
              $output .= '<i class="fa fa-clipboard"></i>';
              $internal_url = \Drupal\Core\Url::fromUserInput('/node/add/article');
              $output .= Link::fromTextAndUrl('添加内容', $internal_url)->toString();
            $output .= '</h5>';
          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';

      $output .= '<div class="alert alert-light" role="alert">';
        $output .= '<div class="row padding-0">';
          $output .= '<div class="col-md-3">';
            $output .= '<h5 class="animated-hover">';
              $output .= 'User';
            $output .= '</h5>';
          $output .= '</div>';
          $output .= '<div class="col-md-5">';
            $output .= '<h5>';
              $output .= '<i class="fa fa-edit"></i>';
              $internal_url = \Drupal\Core\Url::fromUserInput('/adminpage/views/people/list');
              $output .= Link::fromTextAndUrl('管理用户', $internal_url)->toString();
            $output .= '</h5>';
          $output .= '</div>';
          $output .= '<div class="col-md-4">';
            $output .= '<h5>';
              $output .= '<i class="fa fa-clipboard"></i>';
              $internal_url = \Drupal\Core\Url::fromUserInput('/admin/people/create');
              $output .= Link::fromTextAndUrl('添加用户', $internal_url)->toString();
            $output .= '</h5>';
          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';

      $output .= '<div class="alert alert-light" role="alert">';
        $output .= '<div class="row padding-0">';
          $output .= '<div class="col-md-3">';
            $output .= '<h5 class="animated-hover">';
              $output .= 'Term';
            $output .= '</h5>';
          $output .= '</div>';
          $output .= '<div class="col-md-9">';
            $output .= '<h5>';
              $output .= '<i class="fa fa-edit"></i>';
              $output .= '管理分类';
            $output .= '</h5>';
          $output .= '</div>';

          $output .= '<div class="col-md-3">';
          $output .= '</div>';
          $output .= '<div class="col-md-9">';
            $output .= '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">';

              $output .= $this->_termSection('Brand');
              $output .= $this->_termSection('Device');
              $output .= $this->_termSection('Product');
              $output .= $this->_termSection('Solution');
              $output .= $this->_termSection('Feature Details');
              $output .= $this->_termSection('Feature Product');
              $output .= $this->_termSection('Index Block First');
              $output .= $this->_termSection('Index Block Third');

            $output .= '</div>';
          $output .= '</div>';

        $output .= '</div>';
      $output .= '</div>';

    $output .= '</div>';

    return $output;
  }

  /**
   * @return string
   *   Return Hello string.
   */
  public function _termSection($vocabulary_name = NULL) {
    $output = '';

    $collapse_name = 'collapse' . $vocabulary_name;

    $url = Url::fromRoute('entity.taxonomy_term.add_form', ['taxonomy_vocabulary' => strtolower($vocabulary_name)]);
    $link = Link::fromTextAndUrl('Add New', $url)->toString();

    $output .= '<div class="panel panel-default clear-both margin-top-16">';
      $output .= '<div class="display-flex" role="tab">';
        $output .= '<span class="panel-title">';
          $output .= '<a class="btn btn-primary width-100" data-toggle="collapse" href="#' . $collapse_name . '" role="button" aria-expanded="false" aria-controls="collapseExample">';
            $output .= $vocabulary_name;
          $output .= '</a>';
        $output .= '</span>';
        $output .= '<span class="term-add-button-wrapper margin-left-48">';
          $output .= '<div class="btn btn-success">';
            $output .= $link;
          $output .= '</div>';
        $output .= '</div>';
      $output .= '</span>';
      $output .= '<div id="' . $collapse_name . '" class="panel-collapse collapse" role="tabpanel">';
        $output .= '<div class="card card-body">';
          $output .= '<ul>';

          $term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
          $terms = $term_storage->loadByProperties(['vid' => $vocabulary_name]);

          if ($terms && is_array($terms)) {
            foreach ($terms as $term) {
              $output .= '<li>';
                $output .= '<span class="">';
                  $output .= $term->getName();
                $output .= '</span>';
                $output .= '<span class="float-right margin-right-12">';
                  $url = $term->toUrl('edit-form');
                  $output .= Link::fromTextAndUrl('Edit term', $url)->toString();
                $output .= '</span>';
              $output .= '</li>';
            }
          }
          $output .= '</ul>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * @return string
   *   Return Hello string.
   */
  public function _termSection2($vocabulary_name = NULL) {
    $output = '';

    $collapse_name = 'collapse' . $vocabulary_name;

    $url = Url::fromRoute('entity.taxonomy_term.add_form', ['taxonomy_vocabulary' => strtolower($vocabulary_name)]);
    $link = Link::fromTextAndUrl('Add New', $url)->toString();

    $output .= '<div class="panel panel-default">';
      $output .= '<div class="panel-heading" role="tab" id="headingOne">';
        $output .= '<h4 class="panel-title">';
          $output .= '<a role="button" data-toggle="collapse" data-parent="#accordion" href="#' . $collapse_name . '" aria-expanded="false" aria-controls="' . $collapse_name . '">';
            $output .= $vocabulary_name;
          $output .= '</a>';
        $output .= '</h4>';
      $output .= '</div>';
      $output .= '<div class="term-add-button-wrapper margin-left-48">';
        $output .= '<div class="btn btn-success">';
          $output .= $link;
        $output .= '</div>';
      $output .= '</div>';
      $output .= '<div id="' . $collapse_name . '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">';
        $output .= '<div class="panel-body">';
          $output .= '<ul>';

          $term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
          $terms = $term_storage->loadByProperties(['vid' => $vocabulary_name]);

          if ($terms && is_array($terms)) {
            foreach ($terms as $term) {
              $output .= '<li>';
                $output .= '<span class="">';
                  $output .= $term->getName();
                $output .= '</span>';
                $output .= '<span class="float-right margin-right-12">';
                  $url = $term->toUrl('edit-form');
                  $output .= Link::fromTextAndUrl('Edit term', $url)->toString();
                $output .= '</span>';
              $output .= '</li>';
            }
          }
          $output .= '</ul>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }
}
