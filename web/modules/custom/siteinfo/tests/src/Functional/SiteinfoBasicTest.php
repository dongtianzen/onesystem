<?php

declare(strict_types=1);

namespace Drupal\Tests\siteinfo\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\block\Traits\BlockCreationTrait;
use Drupal\Tests\node\Traits\ContentTypeCreationTrait;
use Drupal\Tests\node\Traits\NodeCreationTrait;
use Drupal\Tests\taxonomy\Traits\TaxonomyTestTrait;
use Drupal\user\RoleInterface;

/**
 * Basic smoke tests for the Siteinfo module.
 *
 * Siteinfo defines no routes of its own (no *.routing.yml) -- it only
 * provides a breadcrumb builder (ArticleBreadcrumbBuilder) and a block
 * plugin (SideLinkBlock), both of which run as part of rendering *other*
 * routes (node and taxonomy term pages). These tests place the block and
 * visit those pages to confirm the module does not break rendering.
 *
 * @group custom_modules
 */
class SiteinfoBasicTest extends BrowserTestBase {

  use BlockCreationTrait;
  use ContentTypeCreationTrait;
  use NodeCreationTrait;
  use TaxonomyTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    // Module under test.
    'siteinfo',
    // ArticleBreadcrumbBuilder applies to 'article' nodes and reads
    // field_article_brand; SideLinkBlock reacts to node/taxonomy_term
    // routes and loads terms from the 'brand' vocabulary.
    'node',
    'taxonomy',
    'field',
    // Required to place SideLinkBlock in a region.
    'block',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * The 'brand' vocabulary, shared by all tests in this class.
   *
   * @var \Drupal\taxonomy\VocabularyInterface
   */
  protected $brandVocabulary;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Mirrors the live site's default: 'access content' is granted to
    // anonymous users (see production config/user.role.anonymous.yml). The
    // 'testing' install profile grants nothing by default, so it is set
    // explicitly here.
    user_role_grant_permissions(RoleInterface::ANONYMOUS_ID, ['access content']);

    $this->createContentType(['type' => 'article']);
    // The 'brand' vocabulary must exist before field_brand_storymenu below
    // (a FieldConfig on a taxonomy_term bundle requires the corresponding
    // Vocabulary config entity to already exist).
    $this->brandVocabulary = $this->createVocabulary(['vid' => 'brand', 'name' => 'Brand']);

    $this->createReferenceField('field_article_brand', 'node', 'article', 'taxonomy_term');

    // SideLinkBlock::switchLinkContent() calls
    // $term->get('field_brand_storymenu') for any 'brand' term without
    // checking hasField() first. On the live site this field exists, so
    // this mirrors production schema; without it, any 'brand' vocabulary
    // term page 500s (verified by temporarily omitting this fixture).
    $this->createReferenceField('field_brand_storymenu', 'taxonomy_term', 'brand', 'node');

    // SideLinkBlock's build() runs on every page it's placed on, so place
    // it once here for all tests in this class. The 'testing' install
    // profile places no blocks by default, so the breadcrumb block is
    // placed explicitly too -- otherwise ArticleBreadcrumbBuilder's trail
    // would never be rendered to the page for testBreadcrumb* to check.
    $this->placeBlock('side_link_block', ['region' => 'sidebar_first']);
    $this->placeBlock('system_breadcrumb_block', ['region' => 'content']);
  }

  /**
   * An article node page renders without a fatal error and the
   * ArticleBreadcrumbBuilder + SideLinkBlock both execute without crashing.
   */
  public function testArticleNodePageDoesNotFatal(): void {
    $term = $this->createTerm($this->brandVocabulary, ['name' => 'AcmeBrand']);

    $node = $this->createNode([
      'type' => 'article',
      'title' => 'Siteinfo Fixture Article',
      'status' => 1,
      'field_article_brand' => ['target_id' => $term->id()],
    ]);

    $this->drupalGet($node->toUrl());
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * ArticleBreadcrumbBuilder produces the expected trail: 首页 / 产品中心 /
   * [brand term] / [node title], confirming it builds the same breadcrumb
   * regardless of unrelated module changes.
   */
  public function testArticleBreadcrumbShowsBrandTrail(): void {
    $term = $this->createTerm($this->brandVocabulary, ['name' => 'AcmeBrand']);

    $node = $this->createNode([
      'type' => 'article',
      'title' => 'Siteinfo Fixture Article',
      'status' => 1,
      'field_article_brand' => ['target_id' => $term->id()],
    ]);

    $this->drupalGet($node->toUrl());
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('首页');
    $this->assertSession()->pageTextContains('产品中心');
    $this->assertSession()->pageTextContains('AcmeBrand');
  }

  /**
   * A taxonomy term page (brand vocabulary) does not fatal, exercising the
   * SideLinkBlock::switchLinkContent() taxonomy_term branch.
   */
  public function testBrandTermPageDoesNotFatal(): void {
    $term = $this->createTerm($this->brandVocabulary, ['name' => 'AcmeBrand']);

    $this->drupalGet($term->toUrl());
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Creates a minimal entity_reference field on the given entity/bundle.
   */
  protected function createReferenceField(string $field_name, string $entity_type, string $bundle, string $target_type): void {
    FieldStorageConfig::create([
      'field_name' => $field_name,
      'entity_type' => $entity_type,
      'type' => 'entity_reference',
      'settings' => ['target_type' => $target_type],
    ])->save();

    FieldConfig::create([
      'field_name' => $field_name,
      'entity_type' => $entity_type,
      'bundle' => $bundle,
      'settings' => ['handler' => 'default:' . $target_type],
    ])->save();
  }

}
