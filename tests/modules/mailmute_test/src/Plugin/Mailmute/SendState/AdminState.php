<?php
/**
 * @file
 * Contains \Drupal\mailmute_test\Plugin\Mailmute\SendState\AdminState.
 */

namespace Drupal\mailmute_test\Plugin\Mailmute\SendState;

use Drupal\mailmute\Plugin\Mailmute\SendState\SendStateBase;

/**
 * A send state that requires admin permission to be set.
 *
 * @SendState(
 *   id = "admin_state",
 *   label = "Admin state",
 *   admin = true
 * )
 */
class AdminState extends SendStateBase {
}
