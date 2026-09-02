<?php

declare(strict_types=1);

namespace Drupal\Tests\dashchart\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\node\Traits\ContentTypeCreationTrait;
use Drupal\Tests\node\Traits\NodeCreationTrait;
use Drupal\Tests\taxonomy\Traits\TaxonomyTestTrait;
use Drupal\user\RoleInterface;

/**
 * Basic smoke tests for the Dashchart module routes.
 *
 * @group custom_modules
 */
class DashchartBasicTest extends BrowserTestBase {

  use ContentTypeCreationTrait;
  use NodeCreationTrait;
  use TaxonomyTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    // Module under test.
    'dashchart',
    // All three routes require 'access content'; BrandStatsController also
    // queries nodes and taxonomy terms directly.
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

    // On the live site 'access content' is granted to anonymous users by
    // default (see production config/user.role.anonymous.yml), but the
    // 'testing' install profile used by BrowserTestBase grants nothing.
    // Grant it explicitly so these tests reflect real anonymous access.
    user_role_grant_permissions(RoleInterface::ANONYMOUS_ID, ['access content']);
  }

  /**
   * dashchart.devices: static chart, no database queries.
   */
  public function testDevicesPage(): void {
    $this->drupalGet('/dashchart/devices');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Device Dashboard');
  }

  /**
   * dashchart.compatibility: static chart, no database queries.
   */
  public function testCompatibilityPage(): void {
    $this->drupalGet('/dashchart/compatibility');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Device Compatibility Matrix');
  }

  /**
   * dashchart.brand_stats: verifies the accessCheck(TRUE) node query works
   * and returns the expected brand label, not just a 200 status.
   */
  public function testBrandStatsPageCountsPublishedNodes(): void {
    $this->createContentType(['type' => 'article']);
    $this->createReferenceField('field_article_brand');

    $vocabulary = $this->createVocabulary(['vid' => 'brand', 'name' => 'Brand']);
    $term = $this->createTerm($vocabulary, ['name' => 'AcmeBrand']);

    // Published article referencing the brand term: should be counted.
    $this->createNode([
      'type' => 'article',
      'status' => 1,
      'field_article_brand' => ['target_id' => $term->id()],
    ]);
    // Unpublished article: must NOT be counted (accessCheck/status filter).
    $this->createNode([
      'type' => 'article',
      'status' => 0,
      'field_article_brand' => ['target_id' => $term->id()],
    ]);

    $this->drupalGet('/dashchart/brand-stats');
    $this->assertSession()->statusCodeEquals(200);

    // The brand label and its count are attached via drupalSettings; assert
    // the term label made it into the page and the count reflects only the
    // published node (1), confirming the access-checked query behaves.
    $this->assertSession()->responseContains('AcmeBrand');
    $this->assertSession()->responseContains('"counts":[1]');
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
