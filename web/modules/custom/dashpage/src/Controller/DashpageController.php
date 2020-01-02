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
          $markup .= $this->_brandCanon();
          $markup .= $this->_brandPhabrix();
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
   * @see Twitter Bootstrap 3.2.0 Callout CSS Styles
   */
  public function _brandCanon() {
    $output = '';

    $output .= '<div class="bs-callout bs-callout-danger" id="callout-badges-ie8-empty">';
      $output .= '<div class="row padding-0">';
        $output .= '<div class="col-md-3">';
          $output .= '<h4>CANON</h4>';
        $output .= '</div>';
        $output .= '<div class="col-md-4">';
          $output .= '<p>日本XXX公司</p>';
        $output .= '</div>';
        $output .= '<div class="col-md-5">';
          $output .= '<p>解码和解扰</p>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * @see Twitter Bootstrap 3.2.0 Callout CSS Styles
   */
  public function _brandHarmonic() {
    $output = '';

    $output .= '<div class="bs-callout bs-callout-primary" id="callout-badges-ie8-empty">';
      $output .= '<div class="row padding-0">';
        $output .= '<div class="col-md-3">';
          $output .= '<h4>HARMONIC</h4>';
        $output .= '</div>';
        $output .= '<div class="col-md-4">';
          $output .= '<p>美国哈雷公司</p>';
        $output .= '</div>';
        $output .= '<div class="col-md-5">';
          $output .= '<p>广播级接收器和解码器</p>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * @see Twitter Bootstrap 3.2.0 Callout CSS Styles
   */
  public function _brandPhabrix() {
    $output = '';

    $output .= '<div class="bs-callout bs-callout-warning" id="callout-badges-ie8-empty">';
      $output .= '<div class="row padding-0">';
        $output .= '<div class="col-md-3">';
          $output .= '<h4>PHABRIX</h4>';
        $output .= '</div>';
        $output .= '<div class="col-md-4">';
          $output .= '<p>英国丰播瑞</p>';
        $output .= '</div>';
        $output .= '<div class="col-md-5">';
          $output .= '<p>研发IP测试，发生和监控设备</p>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }


}
