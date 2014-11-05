<?php
/**
 * @file
 * Contains \Drupal\mailmute\Plugin\Mailmute\SendState\OnHold.
 */

namespace Drupal\mailmute\Plugin\Mailmute\SendState;

/**
 * Indicates that the address owner requested muting until further notice.
 *
 * @SendState(
 *   id = "onhold",
 *   label = @Translation("On hold"),
 *   admin = false
 * )
 */
class OnHold extends SendStateBase {

  /**
   * {@inheritdoc}
   */
  public function isMute() {
    return TRUE;
  }

}
