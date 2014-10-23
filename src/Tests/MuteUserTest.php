<?php
/**
 * @file
 * Contains \Drupal\mailmute\Tests\MuteUserTest.
 */

namespace Drupal\mailmute\Tests;
use Drupal\Core\Language\LanguageInterface;
use Drupal\mailmute\SendStateManager;
use Drupal\simpletest\WebTestBase;
use Drupal\user\Entity\User;

/**
 * Tests send states for the user entity.
 *
 * @group mailmute
 */
class MuteUserTest extends WebTestBase {

  /**
   * Modules to enable.
   */
  public static $modules = array('field', 'mailmute');

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

  /**
   * Tests send states for the user entity.
   */
  public function testStates() {
    // A Send state field should be added to User on install.
    $field_map = \Drupal::entityManager()->getFieldMap();
    $this->assert($field_map['user']['field_sendstate']['type'] == 'string');

    /** @var \Drupal\user\UserInterface $user */
    $user = User::create(array(
      'name' => $this->randomMachineName(),
      'mail' => $this->randomMachineName() . '@example.com',
    ));
    $user->save();

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

}
