<?php
/**
 * @file
 * Contains \Drupal\mailmute\Plugin\Mailmute\SendState\OnHold.
 */

namespace Drupal\mailmute\Plugin\Mailmute\SendState;

/**
 * Indicates that the address owner requested muting until further notice.
 *
 * @ingroup plugin
 *
 * @SendState(
 *   id = "onhold",
 *   label = @Translation("On hold"),
 *   mute = true,
 *   admin = false
 * )
 */
class OnHold extends SendStateBase {
}
