<?php
/**
 * @file
 * Contains \Drupal\mailmute\Plugin\Mailmute\SendState\SendStateInterface.
 */

namespace Drupal\mailmute\Plugin\Mailmute\SendState;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Provides methods to interact with a send state.
 */
interface SendStateInterface extends PluginInspectionInterface {

  /**
   * Tells whether to suppress messages to addresses with this state.
   *
   * @return bool
   *   TRUE if messages should be suppressed, FALSE if they should be sent.
   */
  public function isMute();

  /**
   * Render the state for display.
   *
   * @return array
   *   A render array with human-readable information of the state.
   */
  public function display();

  /**
   * Render form elements for state configuration.
   *
   * @return array
   *   A form render array for the state.
   */
  public function form();

}
