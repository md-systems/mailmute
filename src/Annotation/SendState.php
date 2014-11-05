<?php
/**
 * @file
 * Contains \Drupal\mailmute\Annotation\SendState.
 */

namespace Drupal\mailmute\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Annotation for the definition of a send state.
 *
 * @Annotation
 */
class SendState extends Plugin {

  /**
   * The unique, machine-readable name of this state.
   *
   * @var string
   */
  public $id;

  /**
   * The translated, human-readable name of this state.
   *
   * @var string
   */
  public $label;

  /**
   * Whether an administrative permission is required to set the state manually.
   *
   * @var bool
   */
  public $admin;

  /**
   * The plugin id of the parent status.
   *
   * @var string
   */
  public $parent_id;

}
