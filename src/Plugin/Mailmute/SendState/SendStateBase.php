<?php
/**
 * @file
 * Contains \Drupal\mailmute\Plugin\Mailmute\SendState\SendStateBase.
 */

namespace Drupal\mailmute\Plugin\Mailmute\SendState;

use Drupal\Core\Plugin\PluginBase;

/**
 * A send state determines whether messages to an address should be suppressed.
 */
abstract class SendStateBase extends PluginBase implements SendStateInterface {

  /**
   * {@inheritdoc}
   */
  public function display() {
    return array(
      '#markup' => $this->getPluginDefinition()['label'],
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form() {
    return array();
  }

  /**
   * Tells whether to suppress messages to addresses with this state.
   *
   * @return bool
   *   TRUE if messages should be suppressed, FALSE if they should be sent.
   */
  public function isMute() {
    return $this->getPluginDefinition()['mute'];
  }
}
