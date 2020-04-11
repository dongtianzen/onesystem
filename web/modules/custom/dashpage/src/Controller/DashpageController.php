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
    if ($name == 'solution') {
      $markup = $this->_solutionPage();
    }
    else if ($name == 'index') {
      $markup = $this->_indexPage();
    }
    else {
      $markup = $this->_brandPage();
    }

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
  public function _solutionPage() {
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
              $output .= $this->_getTermSolutionHtml();
            $output .= '</div>';

          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * @return string
   *   Return Hello string.
   */
  public function _indexPage() {
    $output = NULL;

    $output .= '<div class="row padding-0">';
      $output .= '<div class="dashpage-index-wrapper">';
        $output .= '<div class="margin-0">';
          $output .= '<div property="schema:text" class="clearfix text-formatted field field--name-body field--type-text-with-summary field--label-hidden field__item">';

            $output .= '<div class="row text-center">';
              $output .= $this->_getIndexRow1Html();
            $output .= '</div>';

            $output .= '<div class="subheader">';
              $output .= '<h5 class="large">';
                $output .= '最新文章';
              $output .= '</h5>';
              $output .= '</div>';
                $output .= $this->_getMostNewArticleList();
              $output .= '</div>';
            $output .= '</div>';

          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * @return string
   */
  public function _getMostNewArticleList() {
    $output = NULL;

    $output .= '<ul class="">';
      $output .= $this->_getMostNewArticleNodeTitle();
    $output .= '</ul>';

    return $output;
  }

  /**
   * @return string
   */
  public function _getMostNewArticleNodeTitle() {
    $output = NULL;

    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('article');
    $query->sort('created', 'DESC');
    $query->range(0, 3);
    $nids = $query_container->runQueryWithGroup($query);
    if ($nids) {
      $nodes = \Drupal::entityManager()
        ->getStorage('node')
        ->loadMultiple($nids);

      if ($nodes) {
        foreach ($nodes as $node) {
          $url = base_path();
          $url .= ltrim(
            \Drupal::service('path.alias_manager')->getAliasByPath('/node/'. $node->id()),
            '/'
          );

          $output .= '<li class="margin-top-12">';
            $output .= '<a href="' . $url . '">';
              $output .= $node->getTitle();
            $output .= '</a>';
          $output .= '</li>';
        }
      }
    }

    return $output;
  }

  /**
   * @return string
   */
  public function _brandPage() {
    $output = '';
    $output .= '<div class="row padding-0">';
      $output .= '<div class="text-center">';
        $output .= '<div class="margin-0">';
          $output .= $this->_brandPageListContent();
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * @return string
   */
  public function _brandPageListContent() {
    $output = '';

    $list = [];
    $list[] = array(
      'class' => 'bs-callout-primar',
      'brand' => 'LiveU',
      'tid' => 27,
      'text' => '以色列LiveU公司',
      'solutions' => array(
        '推动视频直播的革命',
        '基于4G/5G网络的视频直播解决方案',
      ),
    );

    $list[] = array(
      'class' => 'bs-callout-danger',
      'brand' => 'Harmonic',
      'tid' => 27,
      'text' => '美国哈雷公司',
      'solutions' => array(
        '视音频传输领域的专家',
        '广播级视音频传输解决方案',
      ),
    );

    $list[] = array(
      'class' => 'bs-callout-danger',
      'brand' => 'AppearTV',
      'tid' => 191,
      'text' => '挪威AppearTV公司',
      'solutions' => array(
        '传统硬件平台的新玩法',
        '高集成度广播级视音频传输',
      ),
    );

    $list[] = array(
      'class' => 'bs-callout-danger',
      'brand' => 'Ateme',
      'tid' => 191,
      'text' => '法国Ateme公司',
      'solutions' => array(
        '互联网时代视频传输的新玩家',
        '基于CPU架构的视音频处理解决方案',
      ),
    );

    $list[] = array(
      'class' => 'bs-callout-danger',
      'brand' => 'ETL',
      'tid' => 191,
      'text' => '英国ETL公司',
      'solutions' => array(
        '射频领域的专家',
        '卫星射频处理系统全套解决方案',
      ),
    );

    $list[] = array(
      'class' => 'bs-callout-danger',
      'brand' => 'CANON',
      'tid' => 191,
      'text' => '日本佳能公司',
      'solutions' => array(
        '解码和解扰',
      ),
    );

    $list[] = array(
      'class' => 'bs-callout-warning',
      'brand' => 'PHABRIX',
      'tid' => 23,
      'text' => '英国丰播瑞',
      'solutions' => array(
        '研发IP测试，发生和监控设备',
      ),
    );

    $list[] = array(
      'class' => 'bs-callout-warning',
      'brand' => 'PHABRIX',
      'tid' => 23,
      'text' => '英国丰播瑞',
      'solutions' => array(
        '研发IP测试，发生和监控设备',
      ),
    );

    foreach ($list as $key => $row) {
      $output .= $this->_brandPageTemplate(
        $row['class'],
        $row['brand'],
        $row['tid'],
        $row['text'],
        $row['solutions']
      );
    }

    return $output;
  }

  /**
   * @see Twitter Bootstrap 3.2.0 Callout CSS Styles
   */
  public function _brandPageTemplate($class = 'bs-callout-danger', $brand_name = '', $brand_tid = '', $brand_text = '', $solutions = '') {
    $output = '';

    $output .= '<div class="bs-callout '. $class . '" id="callout-badges-ie8-empty">';
      $output .= '<div class="row padding-0">';
        $output .= '<div class="col-md-3">';
          $output .= '<a href= ' . base_path() . 'taxonomy/term/' . $brand_tid . '>';
            $output .= '<h4>';
              $output .= $brand_name;
            $output .= '</h4>';
          $output .= '</a>';
        $output .= '</div>';
        $output .= '<div class="col-md-4">';
          $output .= '<p>';
            $output .= $brand_text;
          $output .= '</p>';
        $output .= '</div>';
        $output .= '<div class="col-md-5">';
          foreach ($solutions as $key => $row) {
            $output .= '<p>';
              $output .= $row;
            $output .= '</p>';
          }
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

}
