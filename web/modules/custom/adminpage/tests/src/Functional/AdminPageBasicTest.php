<?php

declare(strict_types=1);

namespace Drupal\Tests\adminpage\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Basic smoke tests for the AdminPage module routes.
 *
 * @group custom_modules
 */
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
    // DefaultController::_termSection() loads taxonomy_term entities and
    // links to the entity.taxonomy_term.add_form route.
    'taxonomy',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * adminpage.default_controller: /adminpage/table/{name}.
   *
   * DefaultController::adminpageStandard() receives $name but never uses it,
   * so any value is safe here.
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
   * A user with 'access content overview' can load the admin table page.
   */
  public function testTableAuthorizedUserCanAccess(): void {
    $user = $this->drupalCreateUser(['access content overview']);
    $this->drupalLogin($user);

    $this->drupalGet(self::TABLE_PATH);
    $this->assertSession()->statusCodeEquals(200);

    // The controller always renders these static section headers,
    // regardless of the {name} parameter or of taxonomy data present.
    $this->assertSession()->pageTextContains('Content');
    $this->assertSession()->pageTextContains('User');
    $this->assertSession()->pageTextContains('Term');
    $this->assertSession()->responseContains('管理内容');
    $this->assertSession()->responseContains('添加内容');
  }

}
