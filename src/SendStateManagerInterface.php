<?php
/**
 * @file
 * Contains \Drupal\mailmute\SendStateManagerInterface.
 */

namespace Drupal\mailmute;

use Drupal\Component\Plugin\PluginManagerInterface;

/**
 * Provides methods to read and modify the Send State of single mail addresses.
 */
interface SendStateManagerInterface extends PluginManagerInterface {

  /**
   * Get the send state of an address.
   *
   * @param string $address
   *   The mail address whose state should be returned.
   *
   * @return \Drupal\mailmute\Plugin\Mailmute\SendState\SendStateInterface
   *   The current state of the address.
   */
  public function getState($address);

  /**
   * Set the state of an address.
   *
   * @param string $address
   *   The mail address whose state should be set.
   * @param string $state
   *   The plugin id of the new state of the address.
   */
  public function setState($address, $state);

  /**
   * Returns whether the manager manages send states for the given address.
   *
   * @param string $address
   *   An email address.
   *
   * @return bool
   *   Whether send states for the address are managed.
   */
  public function isManaged($address);

}
