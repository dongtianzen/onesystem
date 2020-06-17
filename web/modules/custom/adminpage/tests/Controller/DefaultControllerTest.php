<?php

namespace Drupal\adminpage\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the adminpage module.
 */
class DefaultControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "adminpage DefaultController's controller functionality",
      'description' => 'Test Unit for module adminpage and controller DefaultController.',
      'group' => 'Other',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests adminpage functionality.
   */
  public function testDefaultController() {
    // Check that the basic functions of module adminpage.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
