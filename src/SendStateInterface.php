<?php
/**
 * @file
 * Contains \Drupal\mailmute\SendStateInterface.
 */

namespace Drupal\mailmute;

interface SendStateInterface {

  /**
   * Tells whether to suppress messages to addresses with this state.
   *
   * @return bool
   *   TRUE if messages should be suppressed, FALSE if they should be sent.
   */
  public function isMute();

}
