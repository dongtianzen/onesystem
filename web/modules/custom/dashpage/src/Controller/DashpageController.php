<?php

namespace Drupal\dashpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;


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
  public function dashpageStandardPage($name) {
    if ($name == 'index') {
      $markup = $this->_indexPage();
    }
    else if ($name == 'technologyhub') {
      $markup = $this->_jieshuyuandiPage();
    }
    else if ($name == 'product') {
      $markup = $this->_productPage();
    }
    else if ($name == 'service') {
      $markup = $this->_servicePage();
    }
    else if ($name == 'solution') {
      $markup = $this->_solutionPage();
    }
    else if ($name == 'presscentre') {
      $NewspageController = new NewspageController();
      $markup = $NewspageController->_getTermPresscentreHtml();
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
          // 'dashpage/dashpage-page-style',
          // 'dashpage/animate-css',
          // 'showcase_lite/animate',
        ),
      ),
    );

    return $build;
  }

  /**
   * @return string
   *   Return Hello string.
   */
  public function _getTermProductHtml() {
    $output = NULL;

    $terms = array(
      array(
        'name' => '4G/5G新闻采集',
        'font-class' => 'fa-anchor',
        'tid' => 85,
      ),
      array(
        'name' => '广播级编码转码器',
        'font-class' => 'fa-server',
        'tid' => 82,
      ),
      array(
        'name' => '综合接收解码器',
        'font-class' => 'fa-sort-amount-asc',
        'tid' => 83,
      ),
      array(
        'name' => '传输流综合处理',
        'font-class' => 'fa-gavel',
        'tid' => 84,
      ),
      array(
        'name' => '数字/模拟调制解调',
        'font-class' => 'fa-fax',
        'tid' => 90,
      ),
      array(
        'name' => '卫星射频产品',
        'font-class' => 'fa-magnet',
        'tid' => 88,
      ),
      array(
        'name' => '周边产品',
        'font-class' => 'fa-paperclip',
        'tid' => 87,
      ),
      array(
        'name' => '矩阵',
        'font-class' => 'fa-th-large',
        'tid' => 86,
      ),
      array(
        'name' => '广电测试测量仪器',
        'font-class' => 'fa-wrench',
        'tid' => 89,
      ),
    );

    if ($terms && is_array($terms)) {
      foreach ($terms as $term) {
        $output .= '<div class="col-md-4 col-sm-6">';
          $output .= '<div class="thumbnail clearfix" style="min-hieght: 100px;" value="">';
            $output .= '<h5>';
              $output .= '<span>';
                $internal_url = \Drupal\Core\Url::fromUserInput('/newspage/term/product/' . $term['tid']);
                $output .= Link::fromTextAndUrl($term['name'], $internal_url)->toString();
              $output .= '<span>';
              $output .= '<i class="fa ' . $term['font-class'] . '"></i>';
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
  public function _getTermSolutionHtml() {
    $output = NULL;

    $terms = \Drupal::service('flexinfo.term.service')
      ->getFullTermsFromVidName('solution');

    if ($terms && is_array($terms)) {
      foreach ($terms as $term) {

        // only show term have image
        if ($term->field_solution_image->isEmpty()) {
          continue;
        }

        // image uri
        $image_uri = $term->get('field_solution_image')->entity->getFileUri();
        // image url
        $image_url = $term->get('field_solution_image')->entity->url();

        //
        $details_url = $term->getName();
        if ($term->field_solution_pagelink[0] && $term->field_solution_pagelink[0]->uri) {
          $pagelink_url = \Drupal\Core\Url::fromUri($term->field_solution_pagelink[0]->uri);

          $details_url = Link::fromTextAndUrl($term->getName(), $pagelink_url)->toString();
        }

        $output .= '<div class="col-md-4 col-sm-6">';
          $output .= '<div class="team-member term-solution-page-wrapper min-height-400 clearfix">';
            $output .= '<div class="">';
              $output .= '<span class="term-solution-page-image-wrapper float-left min-height-220 height-220">';
                $output .= '<img class="term-solution-page-image height-210" alt="team member six" src="' . $image_url . '">';
              $output .= '</span>';
            $output .= '</div>';

            $output .= '<div class="clear-both">';
              $output .= '<h5>';
                $output .= '<span>';
                  $output .= $details_url;
                $output .= '<span>';
              $output .= '</h5>';
            $output .= '</div>';

            $output .= '<div class="term-description-wrapper subtitle font-weight-400 clear-both">';
              $output .= $term->get('description')->value;
            $output .= '</div>';

            // $output .= $mylink->toString();

            // $output .= '<ul class="icons-list text-center">';
            //   $output .= '<li class="fn-icon-qq">';
            //     $output .= '<a href="https://www.qq.com/morethan.just.themes/">';
            //       $output .= '<i class="fa fa-qq">';
            //         $output .= '<span class="sr-only">qq</span>';
            //       $output .= '</i>';
            //     $output .= '</a>';
            //   $output .= '</li>';
            //   $output .= '<li class="fn-icon-weixin">';
            //     $output .= '<a href="https://plus.weixin.com/118354321025436191714/posts">';
            //       $output .= '<i class="fa fa-weixin">';
            //         $output .= '<span class="sr-only">Weixin</span>';
            //       $output .= '</i>';
            //     $output .= '</a>';
            //   $output .= '</li>';
            //   $output .= '<li class="fn-icon-linkedin">';
            //     $output .= '<a href="https://www.linkedin.com/company/more-than-themes/">';
            //       $output .= '<i class="fa fa-linkedin">';
            //         $output .= '<span class="sr-only">linkedin</span>';
            //       $output .= '</i>';
            //     $output .= '</a>';
            //   $output .= '</li>';
            // $output .= '</ul>';

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

    $image_url = drupal_get_path('module', 'dashpage') . "/image/home/002.png";

    $output .= '<div class="col-md-4">';
      $output .= '<div class="team-member site-index-page-wrapper min-height-210 clearfix">';
        $output .= '<div class="index-page-image-wrapper min-height-150 height-150">';
          $output .= '<img class="term-solution-page-image height-140" alt="team member six" src="' . $image_url . '">';
        $output .= '</div>';
        $output .= '<div>';
          $output .= '<h5>';
            $output .= '<a href= ' . base_path() . 'taxonomy/term/10>';
              $output .= '<span>';
                $output .= 'LiveU专区';
              $output .= '</span>';
            $output .= '</a>';
          $output .= '</h5>';
          $output .= '<ul class="subtitle">';
            $output .= '<li class="margin-top-12">';
              $output .= '<a href= ' . base_path() . 'node/446>';
                $output .= '超高清直播 在移动中直播';
              $output .= '</a>';
            $output .= '</li>';
            $output .= '<li class="margin-top-12">';
              $output .= '<a href= ' . base_path() . 'node/442>';
                $output .= '超便携4/5G直播传输设备';
              $output .= '</a>';
            $output .= '</li>';
            $output .= '<li class="margin-top-12">';
              $output .= '<a href= ' . base_path() . 'node/157>';
                $output .= 'LU60 4G新闻直播系统';
              $output .= '</a>';
            $output .= '</li>';
          $output .= '</ul>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    $image_url = drupal_get_path('module', 'dashpage') . "/image/home/001.png";

    $output .= '<div class="col-md-4">';
      $output .= '<div class="team-member site-index-page-wrapper min-height-140 clearfix">';
        $output .= '<div class="index-page-image-wrapper min-height-150 height-150">';
          $output .= '<img class="term-solution-page-image height-140" alt="team member six" src="' . $image_url . '">';
        $output .= '</div>';
        $output .= '<div>';
          $output .= '<h5>';
            $output .= '<a href="http://www.onebandrma.com">';
              $output .= '<span>';
                $output .= '维修专区';
              $output .= '</span>';
            $output .= '</a>';
          $output .= '</h5>';
          $output .= '<ul class="subtitle">';
            $output .= '<li class="margin-top-12">';
              $output .= '维修申请页面';
            $output .= '</li>';
            $output .= '<li class="margin-top-12">';
              $output .= '修理查询系统';
            $output .= '</li>';
            $output .= '<li class="margin-top-12">';
              $output .= '紧急配送服务';
            $output .= '</li>';
          $output .= '</ul>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    $image_url = drupal_get_path('module', 'dashpage') . "/image/home/003.png";

    $output .= '<div class="col-md-4">';
      $output .= '<div class="team-member site-index-page-wrapper min-height-140 clearfix">';
        $output .= '<div class="index-page-image-wrapper min-height-150 height-150">';
          $output .= '<img class="term-solution-page-image height-140" alt="team member six" src="' . $image_url . '">';
        $output .= '</div>';
        $output .= '<div>';
          $output .= '<h5>';
            $output .= '<span>';
              $output .= '新产品发布';
            $output .= '</span>';
          $output .= '</h5>';
          $output .= '<div>';
            $output .= $this->_getMostNewArticleList(24);
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
  public function _productPage() {
    $output = NULL;

    $output .= '<div class="row padding-0">';
      $output .= '<div class="text-center">';
        $output .= '<div class="margin-0">';
          $output .= '<div property="schema:text" class="clearfix text-formatted field field--name-body field--type-text-with-summary field--label-hidden field__item">';

            $output .= '<div class="subheader">';
              $output .= '<p class="large">';
                $output .= '产品中心分类';
              $output .= '</p>';
            $output .= '</div>';

            $output .= '<div class="row">';
              $output .= $this->_getTermProductHtml();
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
  public function _servicePage() {
    $output = NULL;

    $output .= '<div class="row padding-0">';
      $output .= '<div class="text-center">';
        $output .= '<div class="margin-0">';
          $output .= '<div property="schema:text" class="clearfix text-formatted field field--name-body field--type-text-with-summary field--label-hidden field__item">';

            $output .= '<div class="subheader">';
              $output .= '<p class="large">';
                $output .= '万博服务';
              $output .= '</p>';
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
  public function _solutionPage() {
    $output = NULL;

    $output .= '<div class="row padding-0">';
      $output .= '<div class="text-center">';
        $output .= '<div class="margin-0">';
          $output .= '<div property="schema:text" class="clearfix text-formatted field field--name-body field--type-text-with-summary field--label-hidden field__item">';

            $output .= '<div class="subheader">';
              $output .= '<p class="large">';
                $output .= '解决方案';
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
              $output .= '<div>';
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
  public function _getMostNewArticleList($max_length = 0) {
    $output = NULL;

    $output .= '<ul class="subtitle">';
      $output .= $this->_getMostNewArticleNodeTitle($max_length);
    $output .= '</ul>';

    return $output;
  }

  /**
   * @return string
   */
  public function _getMostNewArticleNodeTitle($max_length = 0) {
    $output = NULL;

    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('article');
    $query->sort('changed', 'DESC');
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
              if ($max_length > 0) {
                $output .= \Drupal\Component\Utility\Unicode::truncate($node->getTitle(), $max_length);
              }
              else {
                $output .= $node->getTitle();
              }
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
      'class' => 'bs-callout-primary',
      'brand' => 'LiveU',
      'tid' => 10,
      'text' => '以色列LiveU公司',
      'solutions' => array(
        '推动视频直播的革命',
        '基于4G/5G网络的视频直播解决方案',
      ),
    );

    $list[] = array(
      'class' => 'bs-callout-info',
      'brand' => 'Harmonic',
      'tid' => 27,
      'text' => '美国哈雷公司',
      'solutions' => array(
        '视音频传输领域的专家',
        '广播级视音频传输解决方案',
      ),
    );

    $list[] = array(
      'class' => 'bs-callout-success',
      'brand' => 'AppearTV',
      'tid' => 15,
      'text' => '挪威AppearTV公司',
      'solutions' => array(
        '传统硬件平台的新玩法',
        '高集成度广播级视音频传输',
      ),
    );

    $list[] = array(
      'class' => 'bs-callout-default',
      'brand' => 'Ateme',
      'tid' => 134,
      'text' => '法国Ateme公司',
      'solutions' => array(
        '互联网时代视频传输的新玩家',
        '基于CPU架构的视音频处理解决方案',
      ),
    );

    $list[] = array(
      'class' => 'bs-callout-warning',
      'brand' => 'ETL',
      'tid' => 168,
      'text' => '英国ETL公司',
      'solutions' => array(
        '射频领域的专家',
        '卫星射频处理系统全套解决方案',
      ),
    );

    $list[] = array(
      'class' => 'bs-callout-danger',
      'brand' => 'PHABRIX',
      'tid' => 23,
      'text' => '英国Phabrix公司',
      'solutions' => array(
        '专注于广播级视音频测试和测量领域',
        '便携式IP化视频信号测试和测量解决方案',
      ),
    );

    $list[] = array(
      'class' => 'bs-callout-default',
      'brand' => 'Imagine',
      'tid' => 208,
      'text' => '美国Imagine公司',
      'solutions' => array(
        '全球知名的媒体软件和网络化解决方案领域的提供商',
        '制播及周边系统解决方案',
      ),
    );

    $list[] = array(
      'class' => 'bs-callout-primary',
      'brand' => 'Grass Valley',
      'tid' => 209,
      'text' => '美国GV公司',
      'solutions' => array(
        '全球知名的电视节目制作和广播设备提供商',
        '制播及周边系统解决方案',
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
   * @see animate.css
   */
  public function _brandPageTemplate($class = 'bs-callout-danger', $brand_name = '', $brand_tid = '', $brand_text = '', $solutions = '') {
    $output = '';

    $output .= '<div class="bs-callout '. $class . '" id="callout-badges-ie8-empty">';
      $output .= '<div class="row padding-0">';
        $output .= '<div class="col-md-3">';
          $output .= '<a class="animated-hover pulse" href= ' . base_path() . 'newspage/term/brand/' . $brand_tid . '>';
            $output .= '<h4 class="animated-hover pulse">';
              $output .= $brand_name;
            $output .= '</h4>';
          $output .= '</a>';
        $output .= '</div>';
        $output .= '<div class="col-md-4">';
          $output .= '<p class="animated-hover pulse">';
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
