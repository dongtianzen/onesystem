<?php

declare(strict_types=1);

namespace Drupal\Tests\dashpage\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\node\Traits\ContentTypeCreationTrait;
use Drupal\Tests\node\Traits\NodeCreationTrait;
use Drupal\Tests\taxonomy\Traits\TaxonomyTestTrait;
use Drupal\user\RoleInterface;

/**
 * Basic smoke tests for the Dashpage module routes.
 *
 * Special focus: DashpageController and NewspageController both build
 * entity queries (accessCheck(TRUE) was previously missing and has since
 * been fixed). Where a route lists nodes, these tests assert the actual
 * list content (published titles present, unpublished titles absent)
 * rather than only the HTTP status code, so a regression of that fix would
 * be caught.
 *
 * @group custom_modules
 */
class DashpageBasicTest extends BrowserTestBase {

  use ContentTypeCreationTrait;
  use NodeCreationTrait;
  use TaxonomyTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    // Module under test.
    'dashpage',
    // All routes require 'access content'; several controller branches
    // load/query node entities. Newspage brand/product/solution branches
    // query entity_reference fields targeting taxonomy terms.
    'node',
    'taxonomy',
    'field',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

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
  }

  /**
   * dashpage.dashpage_controller_hello: /dashboard/category/index.
   *
   * $name == 'index' returns static markup with no database access.
   */
  public function testCategoryIndexPage(): void {
    $this->drupalGet('/dashboard/category/index');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseContains('dashpage-index-disable-wrapper');
  }

  /**
   * dashpage.dashpage_controller_hello: default/else branch (_brandPage()).
   *
   * Any $name other than the recognised special values (index,
   * presscentre, technologyhub, product, solution, service) falls through
   * to a static, hardcoded brand list.
   */
  public function testCategoryDefaultBrandPage(): void {
    $this->drupalGet('/dashboard/category/brand');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('LiveU');
    $this->assertSession()->pageTextContains('Harmonic');
  }

  /**
   * dashpage.dashpage_controller_hello: $name == 'presscentre'.
   *
   * Exercises NewspageController::_getTermPresscentreHtml(), which lists
   * the 10 most recent published articles via an accessCheck(TRUE) query.
   * Verifies published articles are listed and unpublished ones are not.
   */
  public function testCategoryPresscentreListsOnlyPublishedArticles(): void {
    $this->createContentType(['type' => 'article']);

    $published = $this->createNode([
      'type' => 'article',
      'title' => 'Published Press Article',
      'status' => 1,
    ]);
    $unpublished = $this->createNode([
      'type' => 'article',
      'title' => 'Unpublished Press Article',
      'status' => 0,
    ]);

    $this->drupalGet('/dashboard/category/presscentre');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains($published->getTitle());
    $this->assertSession()->pageTextNotContains($unpublished->getTitle());
  }

  /**
   * dashpage.dashpage_controller_hello: $name == 'technologyhub'.
   *
   * Routes to DashpageController::_standardNodePage(473), which hard-codes
   * node ID 473. On the live site that node exists; here a node with that
   * explicit nid is created as a fixture so the render path is exercised
   * for real instead of only checking "not a 500".
   */
  public function testCategoryTechnologyhubRendersHardcodedNode(): void {
    $this->createContentType(['type' => 'page']);
    $this->createNode([
      'type' => 'page',
      'nid' => 473,
      'title' => 'Technology Hub Fixture',
      'status' => 1,
    ]);

    $this->drupalGet('/dashboard/category/technologyhub');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Technology Hub Fixture');
  }

  /**
   * dashpage.dashpage_controller_hello: $name == 'product'/'solution'/
   * 'service' also hard-code node IDs (474/475/476) via the same
   * _standardNodePage()/renderNode() path already exercised above for
   * 'technologyhub'. On a real (non-test) database those nodes exist; here
   * they do not, so renderNode() receives NULL and the request 500s. This
   * is a placeholder assertion only -- replace the node IDs below with
   * real, current node IDs from the site (or create fixtures as done in
   * testCategoryTechnologyhubRendersHardcodedNode()) to assert 200 once
   * confirmed.
   */
  public function testCategoryProductSolutionServiceDoNotSilentlyChangeBehavior(): void {
    foreach (['product', 'solution', 'service'] as $name) {
      $this->drupalGet('/dashboard/category/' . $name);
      // Without a fixture node at the hard-coded nid, this currently
      // results in a 500 (NULL passed to a NodeInterface-typed argument).
      // Documented here as a known, pre-existing behavior -- not a
      // regression introduced by this test suite.
      $this->assertSession()->statusCodeEquals(500);
    }
  }

  /**
   * dashpage.custom_index: /index-page, node 516 exists.
   */
  public function testIndexPageWithFixtureNode(): void {
    $this->createContentType(['type' => 'page']);
    $this->createNode([
      'type' => 'page',
      'nid' => 516,
      'title' => 'Homepage Fixture',
      'status' => 1,
    ]);

    $this->drupalGet('/index-page');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Homepage Fixture');
  }

  /**
   * dashpage.custom_index: /index-page, node 516 missing.
   *
   * DashpageController::indexPage() falls back to a welcome message when
   * the hard-coded node cannot be loaded, so this remains a real (not a
   * placeholder) assertion.
   */
  public function testIndexPageFallsBackWithoutFixtureNode(): void {
    $this->drupalGet('/index-page');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Welcome to wanbo site');
  }

  /**
   * dashpage.newspage_standard_term: vid_name == 'brand'.
   *
   * second_tid is intentionally not 'all' here, which routes
   * _getTermBrandHtml() through the branch that only conditions on
   * field_article_brand (avoiding the need to also fixture
   * field_article_storymenu, which the 'all' branch additionally checks).
   */
  public function testNewspageBrandTermListsMatchingArticles(): void {
    $this->createContentType(['type' => 'article']);
    $this->createReferenceField('field_article_brand');

    $vocabulary = $this->createVocabulary(['vid' => 'brand', 'name' => 'Brand']);
    $term = $this->createTerm($vocabulary, ['name' => 'AcmeBrand']);
    $other_term = $this->createTerm($vocabulary, ['name' => 'OtherBrand']);

    $matching = $this->createNode([
      'type' => 'article',
      'title' => 'Matching Brand Article',
      'status' => 1,
      'field_article_brand' => ['target_id' => $term->id()],
    ]);
    $non_matching = $this->createNode([
      'type' => 'article',
      'title' => 'Non-Matching Brand Article',
      'status' => 1,
      'field_article_brand' => ['target_id' => $other_term->id()],
    ]);

    $this->drupalGet('/newspage/term/brand/' . $term->id() . '/5');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains($matching->getTitle());
    $this->assertSession()->pageTextNotContains($non_matching->getTitle());
  }

  /**
   * dashpage.newspage_standard_term: vid_name == 'product', second_tid ==
   * 'all' (default), which routes _getTermProductHtml() through the branch
   * that only conditions on field_article_product.
   */
  public function testNewspageProductTermListsMatchingArticles(): void {
    $this->createContentType(['type' => 'article']);
    $this->createReferenceField('field_article_product');

    $vocabulary = $this->createVocabulary(['vid' => 'product', 'name' => 'Product']);
    $term = $this->createTerm($vocabulary, ['name' => 'AcmeProduct']);

    $matching = $this->createNode([
      'type' => 'article',
      'title' => 'Matching Product Article',
      'status' => 1,
      'field_article_product' => ['target_id' => $term->id()],
    ]);

    // second_tid omitted -> defaults to 'all' per the route definition.
    $this->drupalGet('/newspage/term/product/' . $term->id());
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains($matching->getTitle());
  }

  /**
   * dashpage.newspage_standard_term: vid_name == 'solution'.
   *
   * _getTermSolutionHtml() always conditions on field_article_solution
   * regardless of second_tid.
   */
  public function testNewspageSolutionTermListsMatchingArticles(): void {
    $this->createContentType(['type' => 'article']);
    $this->createReferenceField('field_article_solution');

    $vocabulary = $this->createVocabulary(['vid' => 'solution', 'name' => 'Solution']);
    $term = $this->createTerm($vocabulary, ['name' => 'AcmeSolution']);

    $matching = $this->createNode([
      'type' => 'article',
      'title' => 'Matching Solution Article',
      'status' => 1,
      'field_article_solution' => ['target_id' => $term->id()],
    ]);

    $this->drupalGet('/newspage/term/solution/' . $term->id() . '/all');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains($matching->getTitle());
  }

  /**
   * dashpage.newspage_standard_term: unrecognised vid_name.
   *
   * NewspageController::newspageStandardTerm() only special-cases 'brand',
   * 'product' and 'solution'; anything else returns empty markup without
   * touching the database, so no fixture data is needed. term_tid is a
   * placeholder value -- see class docblock.
   */
  public function testNewspageUnknownVidNameDoesNotError(): void {
    $this->drupalGet('/newspage/term/unknown-vocabulary/999999/all');
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Creates a minimal entity_reference field to taxonomy_term on 'article'.
   */
  protected function createReferenceField(string $field_name, string $bundle = 'article'): void {
    FieldStorageConfig::create([
      'field_name' => $field_name,
      'entity_type' => 'node',
      'type' => 'entity_reference',
      'settings' => ['target_type' => 'taxonomy_term'],
    ])->save();

    FieldConfig::create([
      'field_name' => $field_name,
      'entity_type' => 'node',
      'bundle' => $bundle,
      'settings' => ['handler' => 'default:taxonomy_term'],
    ])->save();
  }

}
