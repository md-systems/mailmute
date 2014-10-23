<?php
/**
 * @file
 * Contains \Drupal\mailmute\Tests\MuteUserTest.
 */

namespace Drupal\mailmute\Tests;

use Drupal\Core\Language\LanguageInterface;
use Drupal\simpletest\KernelTestBase;
use Drupal\user\Entity\User;

/**
 * Tests send states for the user entity.
 *
 * @group mailmute
 */
class MuteUserTest extends KernelTestBase {

  /**
   * Modules to enable.
   */
  public static $modules = array('field', 'mailmute', 'user', 'system');

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
    $this->installSchema('system', ['sequences']);
    $this->installSchema('user', ['users_data']);
    $this->installEntitySchema('user');
    $this->installConfig(['mailmute', 'system']);
    \Drupal::config('system.site')->set('mail', 'admin@example.com')->save();
    $this->mailManager = \Drupal::service('plugin.manager.mail');
  }

  /**
   * Tests send states for the user entity.
   */
  public function testStates() {
    // A Send state field should be added to User on install.
    $field_map = \Drupal::entityManager()->getFieldMap();
    $this->assert($field_map['user']['field_sendstate']['type'] == 'string');

    /** @var \Drupal\user\UserInterface $user */
    $user = $this->createUser();

    // Default value should be send.
    $this->assertEqual($user->field_sendstate->value, 'send');

    // Mails should be sent normally.
    $sent = $this->mail($user);
    $this->assertTrue($sent);

    // When value is onhold, mails should not be sent.
    $user->field_sendstate->value = 'onhold';
    $user->save();
    $sent = $this->mail($user);
    $this->assertFalse($sent);
  }

  /**
   * Tests the send state manager methods and the service mechanism.
   */
  public function testSendStateManager() {
    /** @var \Drupal\mailmute\SendStateManager $manager */
    $manager = \Drupal::service('plugin.manager.sendstate');
    $this->assertNotNull($manager, 'Send state manager is loaded');

    // Create a new user and assert default state.
    $user = $this->createUser();

    $this->assertEqual($manager->getState($user->getEmail())->getPluginId(), 'send');
    $this->assertFalse($manager->isMute($user->getEmail()));

    // Set state to On Hold.
    $manager->setState($user->getEmail(), 'onhold');

    $this->assertEqual($manager->getState($user->getEmail())->getPluginId(), 'onhold');
    $this->assertTrue($manager->isMute($user->getEmail()));

    // Reload the user and assert that field persists.
    $user = User::load($user->id());

    $this->assertEqual($manager->getState($user->getEmail())->getPluginId(), 'onhold');
    $this->assertTrue($manager->isMute($user->getEmail()));
  }

  /**
   * Attempts to send a Password reset mail, and indicates success.
   *
   * @param \Drupal\user\UserInterface $user
   *   User object to send email to.
   *
   * @return bool
   *   The result status.
   */
  protected function mail($user) {
    $params = array('account' => $user);
    $message = $this->mailManager->mail('user', 'password_reset', $user->getEmail(), LanguageInterface::LANGCODE_DEFAULT, $params);
    return $message['result'];
  }

  /**
   * Creates a user with a random name and email address.
   *
   * @return \Drupal\user\UserInterface
   *   The created user.
   */
  protected function createUser() {
    $name = $this->randomMachineName();
    $user = User::create(array(
      'name' => $name,
      'mail' => "$name@example.com",
    ));
    $user->save();
    return $user;
  }

}
