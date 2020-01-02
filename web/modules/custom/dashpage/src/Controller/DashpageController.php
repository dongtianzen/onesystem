<?php

namespace Drupal\dashpage\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class DashpageController.
 */
class DashpageController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function hello($name) {
    $markup = NULL;
    $markup .= '<div class="row padding-0">';
      $markup .= '<div class="text-center">';
        $markup .= '<div class="margin-0">';
          $markup .= $this->_brandHarmonic();
        $markup .= '</div>';
      $markup .= '</div>';
    $markup .= '</div>';

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
    );

    return $build;
  }

  /**
   *
   */
  public function _brandHarmonic() {
    $output = '';

    $output .= '<div class="bs-callout bs-callout-danger" id="callout-badges-ie8-empty">';
      $output .= '<div class="row padding-0">';
        $output .= '<div class="col-md-3">';
          $output .= '<h4>HARMONIC</h4>';
        $output .= '</div>';
        $output .= '<div class="col-md-4">';
          $output .= '<p>美国公司</p>';
        $output .= '</div>';
        $output .= '<div class="col-md-4">';
          $output .= '<p>美国公司</p>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

}
