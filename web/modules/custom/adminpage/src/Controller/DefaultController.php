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

      $output .= '<div class="bs-callout bs-callout-primary" id="callout-badges-ie8-empty">';
        $output .= '<div class="row padding-0">';
          $output .= '<div class="col-md-4">';
            $output .= '<h5 class="animated-hover">';
              $output .= 'User';
            $output .= '</h5>';
          $output .= '</div>';
          $output .= '<div class="col-md-4">';
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
          $output .= '<div class="col-md-4">';
            $output .= '<h5 class="animated-hover">';
              $output .= 'Term';
            $output .= '</h5>';
          $output .= '</div>';
          $output .= '<div class="col-md-8">';
            $output .= '<h5>';
              $output .= '<i class="fa fa-edit"></i>';
              $internal_url = \Drupal\Core\Url::fromUserInput('/adminpage/views/content/node');
              $output .= \Drupal::l('管理分类', $internal_url);
            $output .= '</h5>';
          $output .= '</div>';

          $output .= '<div>';
            $output .= '
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
              <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                  <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                      Collapsible Group Item #1
                    </a>
                  </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                  <div class="panel-body">
                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably havent heard of them accusamus labore sustainable VHS.
                  </div>
                </div>
              </div>
              <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingTwo">
                  <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                      Collapsible Group Item #2
                    </a>
                  </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                  <div class="panel-body">
                    2.
                  </div>
                </div>
              </div>
              <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingThree">
                  <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                      Collapsible Group Item #3
                    </a>
                  </h4>
                </div>
                <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                  <div class="panel-body">
                    3.
                  </div>
                </div>
              </div>
            </div>
            ';
          $output .= '</div>';

        $output .= '</div>';
      $output .= '</div>';

    $output .= '</div>';

    return $output;
  }

}
