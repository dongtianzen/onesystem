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
          $markup .= '<span class="bs-callout bs-callout-danger font-weight-400">';
            $markup .=  'Demo page';
          $markup .= '</span>';
        $markup .= '</div>';
      $markup .= '</div>';
    $markup .= '</div>';
    $markup .= '<div class="bs-callout bs-callout-danger" id="callout-badges-ie8-empty">
    <h4>Cross-browser compatibility</h4>
    <p>Badges wont self collapse in Internet Explorer 8 because it lacks support for the <code>:empty</code> selector.</p>
  </div>';


    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
    );

    return $build;
  }

}
