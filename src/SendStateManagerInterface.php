<?php
/**
 * @file
 * Contains \Drupal\mailmute\SendStateManagerInterface.
 */

namespace Drupal\mailmute;

use Drupal\Component\Plugin\PluginManagerInterface;

/**
 * Provides methods to read and modify the Send State of single mail addresses.
 *
 * @ingroup plugin
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
   * Save the previously loaded send state for the given address.
   *
   * @param string $address
   *   An email address.
   */
  public function save($address);

  /**
   * Instantiate the plugin of a new state and save it for the given address.
   *
   * @param string $address
   *   An email address.
   * @param string $plugin_id
   *   The ID of a state plugin.
   * @param array $configuration
   *   Configuration for the state plugin.
   */
  public function transition($address, $plugin_id, array $configuration = array());

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

  /**
   * Returns the send state plugin IDs in a hierarchical structure.
   *
   * @return array
   *   A nested array containing IDs of all plugins as keys, and their children
   *   (as deduced by the parent_id config property) as values.
   */
  public function getPluginIdHierarchy();

}
