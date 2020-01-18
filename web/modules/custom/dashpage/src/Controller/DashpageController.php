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
    $markup = $this->solutionPage();

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
    );

    return $build;
  }

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function solutionPage() {
    $markup = NULL;
    $markup .= '<div class="row padding-0">';
      $markup .= '<div class="text-center">';
        $markup .= '<div class="margin-0">';
          $markup .= '
            $markup .= '<div property="schema:text" class="clearfix text-formatted field field--name-body field--type-text-with-summary field--label-hidden field__item">
              $markup .= '<div class="subheader">';
                $markup .= '<p class="large">Completely drive standardized initiatives with principle-centered ROI. Progressively aggregate emerging content rather than leveraged bandwidth
                  with a touch of uniqueness.';
                $markup .= '</p>';
              $markup .= '</div>';

              $markup .= '<div class="row">';
                $markup .= '<div class="col-md-4 col-sm-6">';
                  $markup .= '<div class="team-member clearfix">';
                    $markup .= '<a class="overlayed" href="#">';
                      $markup .= '<img alt="team member six" src="https://mttprojects.s3.amazonaws.com/demo.morethanthemes.com/showcase-lite/about-6.jpg">';
                    $markup .= '</a>';

                    $markup .= '<h3>';
                      $markup .= '<a href="#">Lorem Ipsum</a>';
                    $markup .= '</h3>';

                    $markup .= '<p class="subtitle">Chief Financial Officer</p>';

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>

                    <ul class="list-unstyled">
                      <li class="phone">
                        <i class="fa fa-phone">
                          <span class="sr-only">phone</span>
                        </i>
                        +1 212-582-8102
                      </li>
                      <li class="email">
                        <i class="fa fa-envelope">
                          <span class="sr-only">email</span>
                        </i>
                        <a href="mailto:lorem.ipsum@showcase-lite.com">lorem.ipsum@showcase-lite.com</a>
                      </li>
                    </ul>

                    <ul class="icons-list text-center">
                      <li class="facebook">
                        <a href="https://www.facebook.com/morethan.just.themes/">
                          <i class="fa fa-facebook">
                            <span class="sr-only">facebook</span>
                          </i>
                        </a>
                      </li>
                      <li class="twitter">
                        <a href="https://twitter.com/morethanthemes/">
                          <i class="fa fa-twitter">
                            <span class="sr-only">twitter</span>
                          </i>
                        </a>
                      </li>
                      <li class="googleplus">
                        <a href="https://plus.google.com/118354321025436191714/posts">
                          <i class="fa fa-google-plus">
                            <span class="sr-only">google plus</span>
                          </i>
                        </a>
                      </li>
                      <li class="linkedin">
                        <a href="https://www.linkedin.com/company/more-than-themes/">
                          <i class="fa fa-linkedin">
                            <span class="sr-only">linkedin</span>
                          </i>
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>';
        $markup .= '</div>';
      $markup .= '</div>';
    $markup .= '</div>';

    return $markup;
  }

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function brandPage() {
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

    return $markup;
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
