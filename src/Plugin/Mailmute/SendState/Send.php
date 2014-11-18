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
 * @ingroup plugin
 *
 * @SendState(
 *   id = "send",
 *   label = @Translation("Send"),
 *   description = @Translation("Messages are not suppressed. This is the default state."),
 *   mute = false,
 *   admin = false
 * )
 */
class Send extends SendStateBase {
}
