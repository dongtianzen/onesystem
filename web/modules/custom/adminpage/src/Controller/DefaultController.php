<?php

namespace Drupal\adminpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;

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
    $output .= '<div class="row">';

      $output .= '<div class="bs-callout bs-callout-primary" id="callout-badges-ie8-empty">';
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

      $output .= '<div class="bs-callout bs-callout-primary" id="callout-badges-ie8-empty">';
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

      $output .= '<div class="bs-callout bs-callout-primary" id="callout-badges-ie8-empty">';
        $output .= '<div class="row padding-0">';
          $output .= '<div class="col-md-3">';
            $output .= '<h5 class="animated-hover">';
              $output .= 'Term';
            $output .= '</h5>';
          $output .= '</div>';
          $output .= '<div class="col-md-9">';
            $output .= '<h5>';
              $output .= '<i class="fa fa-edit"></i>';
              $internal_url = \Drupal\Core\Url::fromUserInput('/adminpage/views/content/node');
              $output .= Link::fromTextAndUrl('管理分类', $internal_url)->toString();
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

    $output .= '<div class="panel panel-default">';
      $output .= '<div class="panel-heading" role="tab" id="headingOne">';
        $output .= '<h4 class="panel-title">';
          $output .= '<a role="button" data-toggle="collapse" data-parent="#accordion" href="#' . $collapse_name . '" aria-expanded="false" aria-controls="' . $collapse_name . '">';
            $output .= $vocabulary_name;
          $output .= '</a>';
        $output .= '</h4>';
      $output .= '</div>';
      $output .= '<div class="margin-left-48">';
        $output .= '<div class="btn btn-success">';
          $output .= \Drupal::service('flexinfo.term.service')
            ->getTermAddLink(strtolower($vocabulary_name), 'Add New');
        $output .= '</div>';
      $output .= '</div>';
      $output .= '<div id="' . $collapse_name . '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">';
        $output .= '<div class="panel-body">';
          $output .= '<ul>';

          $terms = \Drupal::service('flexinfo.term.service')
            ->getFullTermsFromVidName($vocabulary_name);

          if ($terms && is_array($terms)) {
            foreach ($terms as $term) {
              $output .= '<li>';
                $output .= '<span class="">';
                  $output .= $term->getName();
                $output .= '</span>';
                $output .= '<span class="float-right margin-right-12">';
                  $output .= \Drupal::service('flexinfo.term.service')
                    ->getTermEditLink($term->id());
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
