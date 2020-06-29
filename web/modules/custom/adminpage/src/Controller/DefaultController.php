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
          $output .= '<div class="col-md-4">';
            $output .= '<h5 class="animated-hover">';
              $output .= 'Content';
            $output .= '</h5>';
          $output .= '</div>';
          $output .= '<div class="col-md-4">';
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
    $output .= '</div>';

    return $output;
  }

}
