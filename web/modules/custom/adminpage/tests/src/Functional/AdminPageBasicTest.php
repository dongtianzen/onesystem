<?php

declare(strict_types=1);

namespace Drupal\Tests\adminpage\Functional;

use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Tests\BrowserTestBase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Basic smoke tests for the AdminPage overview route.
 *
 * @group custom_modules
 */
#[RunTestsInSeparateProcesses]
class AdminPageBasicTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    // Module under test.
    'adminpage',
    // Defines the 'access content overview' permission required by the
    // adminpage.default_controller route.
    'node',
    // The Taxonomy section lists terms and links to taxonomy routes.
    'taxonomy',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * adminpage.default_controller: /adminpage/table/{name}.
   *
   * The {name} parameter does not change the page, so any value works.
   */
  protected const TABLE_PATH = '/adminpage/table/placeholder-name';

  /**
   * Anonymous users lack 'access content overview' and must be denied.
   */
  public function testTableAnonymousAccessDenied(): void {
    $this->drupalGet(self::TABLE_PATH);
    $this->assertSession()->statusCodeEquals(403);
  }

  /**
   * Links the user cannot access are hidden, and so are empty sections.
   *
   * 'access content overview' grants the FAQ list (system.admin_content), but
   * the article/FAQ node types, the FAQ views and ob_faq do not exist here.
   */
  public function testTableHidesInaccessibleLinks(): void {
    $this->drupalLogin($this->drupalCreateUser(['access content overview']));
    $this->drupalGet(self::TABLE_PATH);
    $assert = $this->assertSession();
    $assert->statusCodeEquals(200);
    $assert->pageTextContains('Admin page');
    $assert->linkExists('FAQ list');
    $assert->linkByHrefExists('/admin/content?type=faq');
    $assert->linkNotExists('Add article');
    $assert->linkNotExists('Add user');
    $assert->elementNotExists('css', '.adminpage-section--content');
    $assert->elementNotExists('css', '.adminpage-section--users');
    $assert->elementNotExists('css', '.adminpage-section--taxonomy');
  }

  /**
   * Listed vocabularies render as a Bootstrap 5 accordion with term links.
   */
  public function testTaxonomyAccordion(): void {
    Vocabulary::create(['vid' => 'brand', 'name' => 'Brand'])->save();
    Vocabulary::create(['vid' => 'tags', 'name' => 'Tags'])->save();
    $term = Term::create(['vid' => 'brand', 'name' => 'Test brand term']);
    $term->save();

    $this->drupalLogin($this->drupalCreateUser([
      'access content overview',
      'administer taxonomy',
    ]));
    $this->drupalGet('/adminpage/table/all');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Taxonomy');

    $assert = $this->assertSession();
    $assert->elementAttributeContains('css', 'button[data-bs-target="#adminpage-vocabulary-brand"]', 'data-bs-toggle', 'collapse');
    $assert->elementExists('css', '#adminpage-vocabulary-brand.accordion-collapse.collapse');
    $assert->elementTextContains('css', 'button[data-bs-target="#adminpage-vocabulary-brand"] .badge', '1');
    $assert->pageTextContains('Test brand term');
    $assert->linkByHrefExists('/admin/structure/taxonomy/manage/brand/add');
    $assert->linkByHrefExists('/taxonomy/term/' . $term->id() . '/edit');
    // Only the vocabularies listed in the controller are shown.
    $assert->elementNotExists('css', '#adminpage-vocabulary-tags');
  }

}
