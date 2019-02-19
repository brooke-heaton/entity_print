<?php

namespace Drupal\entity_email\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the entity_email module.
 */
class EmailEnitityControllerTest extends WebTestBase {
  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "entity_email EmailEnitityController's controller functionality",
      'description' => 'Test Unit for module entity_email and controller EmailEnitityController.',
      'group' => 'Other',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests entity_email functionality.
   */
  public function testEmailEnitityController() {
    // Check that the basic functions of module entity_email.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via App Console.');
  }

}
