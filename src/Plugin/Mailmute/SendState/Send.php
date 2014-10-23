<?php
/**
 * @file
 * Contains \Drupal\mailmute\Plugin\Mailmute\SendState\Send.
 */

namespace Drupal\mailmute\Plugin\Mailmute\SendState;

/**
 * Indicates that messages should not be suppressed.
 *
 * This is the default state and is equivalent to not applying send states at
 * all.
 *
 * @SendState(
 *   id = "send",
 *   label = @Translation("Send"),
 * )
 */
class Send extends SendStateBase {

  /**
   * {@inheritdoc}
   */
  public function isMute() {
    return FALSE;
  }

}
