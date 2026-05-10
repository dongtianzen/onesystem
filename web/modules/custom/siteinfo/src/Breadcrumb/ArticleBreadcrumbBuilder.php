<?php

namespace Drupal\siteinfo\Breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;

/**
 * Builds breadcrumbs for article nodes.
 *
 * Trail: 首页 / 产品中心 / [Brand term] / [Node title]
 */
class ArticleBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    $node = $route_match->getParameter('node');
    return $node instanceof NodeInterface && $node->bundle() === 'article';
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();

    /** @var \Drupal\node\NodeInterface $node */
    $node = $route_match->getParameter('node');

    // 首页
    $breadcrumb->addLink(Link::createFromRoute($this->t('首页'), '<front>'));

    // 产品中心 — /dashboard/category/product
    $breadcrumb->addLink(Link::fromTextAndUrl(
      $this->t('产品中心'),
      Url::fromUserInput('/dashboard/category/product')
    ));

    // Brand term — field_article_brand（词汇表：brand）
    if (!$node->get('field_article_brand')->isEmpty()) {
      $brand_terms = $node->get('field_article_brand')->referencedEntities();
      $brand_term = reset($brand_terms);
      if ($brand_term) {
        $breadcrumb->addLink(Link::createFromRoute(
          $brand_term->getName(),
          'entity.taxonomy_term.canonical',
          ['taxonomy_term' => $brand_term->id()]
        ));
        $breadcrumb->addCacheableDependency($brand_term);
      }
    }

    // 当前节点标题（末尾项，无链接）
    $breadcrumb->addLink(Link::createFromRoute($node->getTitle(), '<nolink>'));

    // 缓存：随路由和节点/term 内容失效
    $breadcrumb->addCacheContexts(['route']);
    $breadcrumb->addCacheableDependency($node);

    return $breadcrumb;
  }

}
