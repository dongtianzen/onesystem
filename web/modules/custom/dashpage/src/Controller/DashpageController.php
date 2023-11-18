<?php

namespace Drupal\dashpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;



/**
 * Class DashpageController.
 */
class DashpageController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * MyController constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

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

    if ($name == 'technologyhub') {
      $build = $this->_standardNodePage(473);
    }
    else if ($name == 'product') {
      $build = $this->_standardNodePage(474);
    }
    else if ($name == 'solution') {
      $build = $this->_standardNodePage(475);
    }
    else if ($name == 'service') {
      $build = $this->_standardNodePage(476);
    }

    return $build;
  }

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function indexPage() {
    $index_node = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->load(516);

    if ($index_node) {
      $node_view = \Drupal::entityTypeManager()
        ->getViewBuilder('node')
        ->view($index_node, 'full');

      return $node_view;
    }

    return [
      '#markup' => $this->t('Welcome to wanbo site'),
    ];
  }

  /**
   * @return string
   *   Return Hello string.
   */
  public function _standardNodePage($nid) {
    $node_storage = $this->entityTypeManager->getStorage('node');
    $node = $node_storage->load($nid);
    $output = $this->renderNode($node);

    return $output;
  }

  /**
   * @return string
   *   Return Hello string.
   */
  public function _indexPage() {
    $output = '';
    $output .= '<div class="dashpage-index-disable-wrapper">';
    $output .= '</div>';
    return $output;

      $output .= '<div class="dashpage-index-wrapper">';
        $output .= '<div class="margin-0">';
          $output .= '<div class="clearfix">';

            $output .= '<div class="row padding-0">';

              $output .= '<div class="col-md-6 col-sm-12 col-xs-12">';
                // $output .= $this->_getIndexGridFromParagraphs(483);
              $output .= '</div>';

              $output .= '<div class="col-sm-12 col-xs-12">';
                $output .= '<hr />';
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
      $output .= '</div>';

    return $output;
  }

  /**
   * @return string
   *   Return Hello string.
   */
  public function _getIndexGridFromParagraphs($nid = NULL) {
    $output = NULL;

    $node = $this->entityTypeManager->getStorage('node')->load($nid);
    $paragraph_values = $node->get('field_custom_grid_paragraphs')->getValue();
    foreach ($paragraph_values as $paragraph_item) {
      $paragraph_id = $paragraph_item['target_id'];
      $paragraph = \Drupal::entityTypeManager()->getStorage('paragraph')->load($paragraph_id);

      $header = $paragraph->field_grid_layout_header->value;
      $images = $paragraph->get('field_grid_layout_image')->getValue();
      $links = $paragraph->get('field_grid_layout_link')->getValue();

      $image_render_array = [];
      if (isset($images[0])) {
        $image_file = \Drupal\file\Entity\File::load($images[0]['target_id']);
        if ($image_file) {
          $image_render_array = [
            '#theme' => 'image_style',
            '#style_name' => 'medium',
            '#uri' => $image_file->getFileUri(),
            '#alt' => 'Alt text',
            '#title' => 'Title text',
          ];
        }
      }

      $output .= '<div class="col-md-4">';
        $output .= '<div class="team-member site-index-page-wrapper min-height-210 clearfix">';
          $output .= '<div class="index-page-image-wrapper min-height-150 height-150">';
            $output .= \Drupal::service('renderer')->renderRoot($image_render_array);
          $output .= '</div>';
          $output .= '<div>';
            $output .= '<h5>';
              $output .= '<span>';
                $output .= $header;
              $output .= '</span>';
            $output .= '</h5>';
            $output .= '<ul class="subtitle">';
              foreach ($links as $link_item) {
                $output .= '<li class="margin-top-12">';
                  $uri = $link_item['uri'];
                  $output .= \Drupal\Core\Link::fromTextAndUrl($link_item['title'], \Drupal\Core\Url::fromUri($uri))->toString();;
                $output .= '</li>';
              }
            $output .= '</ul>';
          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';
    }

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

    $query = \Drupal::entityQuery('node')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('type', 'article')
      ->sort('created', 'DESC')
      ->range(0, 5);
    $nids = $query->execute();

    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
    if ($nodes) {
      foreach ($nodes as $node) {
        $url = base_path();
        $url .= ltrim(
          \Drupal::service('path_alias.manager')->getAliasByPath('/node/'. $node->id()),
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

    foreach ($list as $row) {
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
          foreach ($solutions as $row) {
            $output .= '<p>';
              $output .= $row;
            $output .= '</p>';
          }
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * Load and render a node.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to render.
   *
   * @return array
   *   A render array representing the node content.
   */
  public function renderNode(NodeInterface $node) {
    $view_builder = $this->entityTypeManager->getViewBuilder('node');
    $build = $view_builder->view($node, 'full');

    return $build;
  }

}
