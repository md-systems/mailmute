<?php
/**
 * @file
 * Contains \Drupal\mailmute\Plugin\Mailmute\SendState\SendStateBase.
 */

namespace Drupal\mailmute\Plugin\Mailmute\SendState;

use Drupal\Core\Plugin\PluginBase;
use Drupal\mailmute\SendStateInterface;

/**
 * A send state determines whether messages to an address should be suppressed.
 */
abstract class SendStateBase extends PluginBase implements SendStateInterface {

}
