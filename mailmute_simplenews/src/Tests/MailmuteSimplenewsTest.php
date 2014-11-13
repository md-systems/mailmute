<?php
/**
 * @file
 * Contains \Drupal\mailmute_simplenews\Tests\MailmuteSimplenewsTest.
 */

namespace Drupal\mailmute_simplenews\Tests;

use Drupal\Core\Language\LanguageInterface;
use Drupal\mailmute\Tests\MailmuteKernelTestBase;
use Drupal\simplenews\Entity\Subscriber;
use Drupal\simplenews\Source\SourceTest;

/**
 * Tests send state for the Simplenews Subscriber entity.
 *
 * @group mailmute
 */
class MailmuteSimplenewsTest extends MailmuteKernelTestBase {

  /**
   * Modules to enable.
   */
  public static $modules = array(
    'simplenews',
    'field',
    'mailmute',
    'mailmute_simplenews',
    'user',
    'system',
  );

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installSchema('system', ['sequences']);
    $this->installEntitySchema('user');
    $this->installEntitySchema('simplenews_subscriber');
    $this->installConfig(['mailmute', 'mailmute_simplenews', 'system']);
    \Drupal::config('system.site')->set('mail', 'admin@example.com')->save();
  }

  /**
   * Tests send states for the Subscriber entity.
   */
  public function testStates() {
    // A Send state field should be added to Subscriber on install.
    $field_map = \Drupal::entityManager()->getFieldMap();
    $this->assertEqual($field_map['simplenews_subscriber']['field_sendstate']['type'], 'sendstate');

    $name = $this->randomMachineName();
    /** @var \Drupal\simplenews\Entity\Subscriber $subscriber */
    $subscriber = Subscriber::create(array(
      'mail' => "$name@example.com",
    ));

    // Default plugin_id should be send.
    $this->assertEqual($subscriber->field_sendstate->plugin_id, 'send');

    // Mails should be sent normally.
    $sent = $this->mail($subscriber);
    $this->assertTrue($sent);

    // When plugin_id is onhold, mails should not be sent.
    $subscriber->field_sendstate->plugin_id = 'onhold';
    $subscriber->save();
    $sent = $this->mail($subscriber);
    $this->assertFalse($sent);
  }

  /**
   * Attempts to send a Simplenews test mail, and indicates success.
   *
   * @param \Drupal\simplenews\SubscriberInterface $subscriber
   *   Subscriber object to send email to.
   *
   * @return bool
   *   The result status.
   */
  protected function mail($subscriber) {
    $params = array('simplenews_source' => new SourceTest('plain'));
    $message = $this->mailManager->mail('simplenews', 'test', $subscriber->getMail(), LanguageInterface::LANGCODE_DEFAULT, $params);
    return $message['result'];
  }

}
