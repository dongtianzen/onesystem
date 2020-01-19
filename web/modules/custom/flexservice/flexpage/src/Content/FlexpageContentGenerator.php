<?php

/**
 * @file
 */

namespace Drupal\flexpage\Content;

use Drupal\Core\Controller\ControllerBase;

use Drupal\flexpage\Content\FlexpageBlockGenerator;

/**
 * An example controller.
 $FlexpageContentGenerator = new FlexpageContentGenerator();
 $FlexpageContentGenerator->angularPage();
 */
class FlexpageContentGenerator extends ControllerBase {

  /**
   *
   */
  public function angularSnapshotWrapper() {
    $FlexpageBlockGenerator = new FlexpageBlockGenerator();

    $output = '';
    $output .= '<div id="pageInfoBase" data-ng-app="pageInfoBase" class="pageinfo-subpage-common margin-left-60 margin-right-44">';
      $output .= '<div data-ng-controller="PageInfoBaseController" class="row margin-0" ng-cloak>';
        $output .= '<div data-ng-controller="SaveAsPng">';

          $output .= '<div class="block-one padding-bottom-20">';
            $output .= '<div class="row">';
              $output .= $FlexpageBlockGenerator->topWidgetsFixed();
            $output .= '</div>';
          $output .= '</div>';

          // for load spinner
          $output .= '<div id="spinner-center-wrapper" class="fixed-center"></div>';

          $output .= '<div id="charts-section" class="block-three row tab-content-block-wrapper">';
            $output .= '<div data-ng-repeat="block in pageData.contentSection" >';
              $output .= '<div class="{{block.class}}">';
                $output .= $FlexpageBlockGenerator->contentBlockMaster();
              $output .= '</div>';
            $output .= '</div>';
          $output .= '</div>';

        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

}
