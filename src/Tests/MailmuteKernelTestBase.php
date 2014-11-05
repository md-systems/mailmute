<?php
/**
 * @file
 * Contains \Drupal\mailmute\Tests\MailmuteKernelTestBase.
 */

namespace Drupal\mailmute\Tests;

use Drupal\simpletest\KernelTestBase;

/**
 * Test base for Mailmute kernel tests.
 */
abstract class MailmuteKernelTestBase extends KernelTestBase {

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManager
   */
  protected $mailManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->mailManager = \Drupal::service('plugin.manager.mail');
  }

}
