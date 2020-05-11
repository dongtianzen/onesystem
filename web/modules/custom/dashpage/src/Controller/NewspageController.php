<?php

namespace Drupal\dashpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */
class NewspageController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   * dpm($request->getpathInfo());
   */
  public function newspageStandardTerm($vid_name, $term_tid = NULL, $second_tid = NULL, Request $request) {
    $markup = '';
    if ($vid_name == 'brand') {
      $markup = $this->_getTermBrandHtml();
    }

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
    );

    return $build;
  }

  /**
   * @return string
   *   Return Hello string.
   */
  public function _getTermSolutionHtml() {
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

        $output .= '<div class="col-md-4 col-sm-6">';
          $output .= '<div class="team-member term-solution-page-wrapper clearfix">';
            $output .= '<a class="overlayed" href=" ' . base_path() . 'taxonomy/term/' . $term->id()  .'">';
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
  public function _getIndexRow1Html() {
    $output = NULL;

    $output .= '<div class="col-md-4">';
      $output .= '<div class="team-member site-index-page-wrapper clearfix">';
        $output .= '<h5>';
          $output .= '<a href= ' . base_path() . 'taxonomy/term/10>';
            $output .= '<span>';
              $output .= 'LiveU专区';
            $output .= '</span>';
          $output .= '</a>';
        $output .= '</h5>';
        $output .= '<p class="subtitle">看不清</p>';
      $output .= '</div>';
    $output .= '</div>';

    $output .= '<div class="col-md-4">';
      $output .= '<div class="team-member site-index-page-wrapper clearfix">';
        $output .= '<h5>';
          $output .= '<a href="http://www.onebandrma.com">';
            $output .= '<span>';
              $output .= '维修专区';
            $output .= '</span>';
          $output .= '</a>';
        $output .= '</h5>';
        $output .= '<p class="subtitle">OnebandRMA</p>';
      $output .= '</div>';
    $output .= '</div>';

    $output .= '<div class="col-md-4">';
      $output .= '<div class="team-member site-index-page-wrapper clearfix">';
        $output .= '<h5>';
          $output .= '<span>';
            $output .= '新产品发布';
          $output .= '</span>';
        $output .= '</h5>';
        $output .= '<ul class="subtitle">';
          $output .= '<li class="">';
              $output .= '手动文章';
          $output .= '</li>';
          $output .= '<li class="">';
              $output .= '手动文章';
          $output .= '</li>';
          $output .= '<li class="">';
              $output .= '手动文章';
          $output .= '</li>';
        $output .= '</ul>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * @return string
   *   Return Hello string.
   */
  public function _jieshuyuandiPage() {
    $output = NULL;

    $output .= '<div class="row padding-0">';
      $output .= '<div class="text-center">';
        $output .= '<div class="margin-0">';
          $output .= '<div property="schema:text" class="clearfix">';

            $output .= '<div class="subheader">';
              $output .= '<p class="large">';
              $output .= '欢迎来到技术园地';
              $output .= '</p>';
            $output .= '</div>';

            $output .= '<div class="row text-left">';

              $output .= '<div class="col-md-4 col-sm-6">';
                $output .= '<div class="subheader">';
                  $output .= '<h5 class="large">';
                    $output .= '最火文章';
                  $output .= '</h5>';
                $output .= '</div>';
                $output .= '<div>';
                  $content = views_embed_view('custom_view_node_article', 'embed_1');
                  $output .= \Drupal::service('renderer')->renderRoot($content);
                $output .= '</div>';
              $output .= '</div>';

              $output .= '<div class="col-md-4 col-sm-6">';
                $output .= '<div class="subheader">';
                  $output .= '<h5 class="large">';
                    $output .= '最新文章';
                  $output .= '</h5>';
                $output .= '</div>';
                $output .= '<div>';
                  $content = views_embed_view('custom_view_node_article', 'embed_1');
                  $output .= \Drupal::service('renderer')->renderRoot($content);
                $output .= '</div>';
              $output .= '</div>';

              $output .= '<div class="col-md-4 col-sm-6">';
                $output .= '<div class="subheader">';
                  $output .= '<h5 class="large">';
                    $output .= '最近更新';
                  $output .= '</h5>';
                $output .= '</div>';
                $output .= '<div>';
                  $content = views_embed_view('custom_view_node_article', 'embed_1');
                  $output .= \Drupal::service('renderer')->renderRoot($content);
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
