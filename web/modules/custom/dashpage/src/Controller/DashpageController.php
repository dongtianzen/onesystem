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
      '#attached' => array(
        'library' => array(
          'dashpage/dashpage-page-style',
        ),
      ),
    );

    return $build;
  }

  /**
   * @return string
   *   Return Hello string.
   */
  public function getTermSolutionHtml() {
    $output = NULL;

    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('solution');

    if ($terms && is_array($terms)) {
      foreach ($terms as $term) {

        // only show term have image
        if ($term->field_solution_image->isEmpty()) {
          continue;
        }

        // image uri
        $uri = $term->get('field_solution_image')->entity->getFileUri();

        // url
        $url = $term->get('field_solution_image')->entity->url();

        // specify large style url
        $styled_image_url = \Drupal\image\Entity\ImageStyle::load('large')->buildUrl($uri);

        $output .= '<div class="col-md-4 col-sm-6" style="min-height:500px;">';
          $output .= '<div class="team-member term-solution-page-wrapper clearfix ">';
            $output .= '<a class="overlayed" href="#">';
              $output .= '<span class="term-solution-page-image-wrapper">';
                $output .= '<img class="term-solution-page-image" alt="team member six" src="' . $url . '">';
              $output .= '</span>';
            $output .= '</a>';

            $output .= '<h5>';
              $output .= '<span>';
                $output .= $term->getName();
              $output .= '<span>';
            $output .= '</h5>';

            // $output .= '<p class="subtitle">Chief Financial Officer</p>';

            $output .= '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>';

            // $output .= '<ul class="list-unstyled">';
            //   $output .= '<li class="phone">';
            //     $output .= '<i class="fa fa-phone">';
            //       $output .= '<span class="sr-only">phone</span>';
            //     $output .= '</i>';
            //     $output .= '<span>+1 212-582-8102</span>';
            //   $output .= '</li>';
            //   $output .= '<li class="email">';
            //     $output .= '<i class="fa fa-envelope">';
            //       $output .= '<span class="sr-only">email</span>';
            //     $output .= '</i>';
            //     $output .= '<a href="mailto:lorem.ipsum@showcase-lite.com">lorem.ipsum@showcase-lite.com</a>';
            //   $output .= '</li>';
            // $output .= '</ul>';

            $output .= '<ul class="icons-list text-center">';
              $output .= '<li class="fn-icon-qq">';
                $output .= '<a href="https://www.qq.com/morethan.just.themes/">';
                  $output .= '<i class="fa fa-qq">';
                    $output .= '<span class="sr-only">qq</span>';
                  $output .= '</i>';
                $output .= '</a>';
              $output .= '</li>';
              $output .= '<li class="fn-icon-weixin">';
                $output .= '<a href="https://plus.weixin.com/118354321025436191714/posts">';
                  $output .= '<i class="fa fa-weixin">';
                    $output .= '<span class="sr-only">Weixin</span>';
                  $output .= '</i>';
                $output .= '</a>';
              $output .= '</li>';
              $output .= '<li class="fn-icon-linkedin">';
                $output .= '<a href="https://www.linkedin.com/company/more-than-themes/">';
                  $output .= '<i class="fa fa-linkedin">';
                    $output .= '<span class="sr-only">linkedin</span>';
                  $output .= '</i>';
                $output .= '</a>';
              $output .= '</li>';
            $output .= '</ul>';

          $output .= '</div>';
        $output .= '</div>';
      }
    }

    return $output;
  }

  /**
   * @return string
   *   Return Hello string.
   */
  public function solutionPage() {
    $output = NULL;

    $output .= '<div class="row padding-0">';
      $output .= '<div class="text-center">';
        $output .= '<div class="margin-0">';
          $output .= '<div property="schema:text" class="clearfix text-formatted field field--name-body field--type-text-with-summary field--label-hidden field__item">';

            $output .= '<div class="subheader">';
              $output .= '<p class="large">Completely drive standardized initiatives with principle-centered ROI. Progressively aggregate emerging content rather than leveraged bandwidth
                with a touch of uniqueness.';
              $output .= '</p>';
            $output .= '</div>';

            $output .= '<div class="row">';
              $output .= $this->getTermSolutionHtml();
            $output .= '</div>';

          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
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
