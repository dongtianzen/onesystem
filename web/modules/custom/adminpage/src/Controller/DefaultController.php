<?php

namespace Drupal\adminpage\Controller;

use Drupal\Core\Controller\ControllerBase;

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
              $output .= \Drupal::l('管理内容', $internal_url);
            $output .= '</h5>';
          $output .= '</div>';
          $output .= '<div class="col-md-4">';
            $output .= '<h5>';
              $output .= '<i class="fa fa-clipboard"></i>';
              $internal_url = \Drupal\Core\Url::fromUserInput('/node/add/article');
              $output .= \Drupal::l('添加内容', $internal_url);
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
              $output .= \Drupal::l('管理用户', $internal_url);
            $output .= '</h5>';
          $output .= '</div>';
          $output .= '<div class="col-md-4">';
            $output .= '<h5>';
              $output .= '<i class="fa fa-clipboard"></i>';
              $internal_url = \Drupal\Core\Url::fromUserInput('/admin/people/create');
              $output .= \Drupal::l('添加用户', $internal_url);
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
              $output .= \Drupal::l('管理分类', $internal_url);
            $output .= '</h5>';
          $output .= '</div>';

          $output .= '<div class="col-md-3">';
          $output .= '</div>';
          $output .= '<div class="col-md-9">';
            $output .= '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">';

              $output .= '<div class="panel panel-default">';
                $output .= '<div class="panel-heading" role="tab" id="headingOne">';
                  $output .= '<h4 class="panel-title">';
                    $output .= '<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">';
                      $output .= 'Brand';
                    $output .= '</a>';
                  $output .= '</h4>';
                $output .= '</div>';
                $output .= '<div class="margin-left-48">';
                  $output .= '<div class="btn btn-success">';
                    $output .= \Drupal::service('flexinfo.term.service')->getTermAddLink('brand', 'Add New');
                  $output .= '</div>';
                $output .= '</div>';
                $output .= '<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">';
                  $output .= '<div class="panel-body">';
                    $output .= '<ul>';
                    $terms = \Drupal::service('flexinfo.term.service')
                      ->getFullTermsFromVidName('Brand');
                    if ($terms && is_array($terms)) {
                      foreach ($terms as $term) {
                        $output .= '<li>';
                          $output .= $term->getName();
                        $output .= '</li>';
                      }
                    }
                    $output .= '</ul>';
                  $output .= '</div>';
                $output .= '</div>';
              $output .= '</div>';

              $output .= '<div class="panel panel-default">';
                $output .= '<div class="panel-heading" role="tab" id="headingTwo">';
                  $output .= '<h4 class="panel-title">';
                    $output .= '<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">';
                      $output .= 'Device';
                    $output .= '</a>';
                  $output .= '</h4>';
                $output .= '</div>';
                $output .= '<div class="margin-left-48">';
                  $output .= '<div class="btn btn-success">';
                    $output .= \Drupal::service('flexinfo.term.service')->getTermAddLink('device', 'Add New');
                  $output .= '</div>';
                $output .= '</div>';
                $output .= '<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">';
                  $output .= '<div class="panel-body">';
                    $terms = \Drupal::service('flexinfo.term.service')
                      ->getFullTermsFromVidName('Device');
                    if ($terms && is_array($terms)) {
                      foreach ($terms as $term) {
                        $output .= '<div>';
                          $output .= $term->getName();
                        $output .= '</div>';
                      }
                    }
                  $output .= '</div>';
                $output .= '</div>';
              $output .= '</div>';

              $output .= '<div class="panel panel-default">';
                $output .= '<div class="panel-heading" role="tab" id="headingThree">';
                  $output .= '<h4 class="panel-title">';
                    $output .= '<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">';
                      $output .= 'Product';
                    $output .= '</a>';
                  $output .= '</h4>';
                $output .= '</div>';
                $output .= '<div class="margin-left-48">';
                  $output .= '<div class="btn btn-success">';
                    $output .= \Drupal::service('flexinfo.term.service')->getTermAddLink('product', 'Add New');
                  $output .= '</div>';
                $output .= '</div>';
                $output .= '<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">';
                  $output .= '<div class="panel-body">';
                    $terms = \Drupal::service('flexinfo.term.service')
                      ->getFullTermsFromVidName('Product');
                    if ($terms && is_array($terms)) {
                      foreach ($terms as $term) {
                        $output .= '<div>';
                          $output .= $term->getName();
                        $output .= '</div>';
                      }
                    }
                  $output .= '</div>';
                $output .= '</div>';
              $output .= '</div>';

            $output .= '</div>';
          $output .= '</div>';

        $output .= '</div>';
      $output .= '</div>';

    $output .= '</div>';

    return $output;
  }

}
