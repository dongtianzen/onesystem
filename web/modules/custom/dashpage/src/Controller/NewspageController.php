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
      $markup = $this->_getTermBrandHtml($term_tid, $second_tid);
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
  public function _getTermBrandHtml($term_tid = NULL, $second_tid = NULL) {
    $output = NULL;
                $output .= 'ppp';

    $query = \Drupal::service('flexinfo.querynode.service')
      ->queryNidsByBundle('article');
    $group = \Drupal::service('flexinfo.querynode.service')
      ->groupStandardByFieldValue($query, $field_name = 'field_article_brand', $term_tid);
    $query->condition($group);

    $nids = \Drupal::service('flexinfo.querynode.service')
      ->runQueryWithGroup($query);
    $nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($nids);

    if ($nodes && is_array($nodes)) {
      foreach ($nodes as $node) {

        $output .= '<div class="col-md-4 col-sm-6">';
          $output .= '<div class="team-member term-solution-page-wrapper clearfix">';
            $output .= '<span class="term-solution-page-image-wrapper">';
            $output .= '</span>';

            $output .= '<h5>';
              $output .= '<span>';
                $output .= $node->getTitle();
              $output .= '<span>';
            $output .= '</h5>';

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
