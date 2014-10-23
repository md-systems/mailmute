<?php
/**
 * @file
 * Contains \Drupal\mailmute\SendStateManagerInterface.
 */

namespace Drupal\mailmute;

/**
 * Provides methods to read and modify the Send State of single mail addresses.
 */
interface SendStateManagerInterface {

  /**
   * Get the send state of an address.
   *
   * @param string $address
   *   The mail address whose state should be returned.
   *
   * @return \Drupal\mailmute\SendStateInterface
   *   The current state of the address.
   */
  public function getState($address);

  /**
   * Set the state of an address.
   *
   * @param string $address
   *   The mail address whose state should be set.
   * @param \Drupal\mailmute\SendStateInterface $state
   *   The new state of the address: FALSE for mute, TRUE for send.
   */
  public function setState($address, SendStateInterface $state);

}
