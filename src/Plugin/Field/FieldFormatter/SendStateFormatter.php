<?php
/**
 * @file
 * Contains \Drupal\mailmute\Plugin\Field\FieldFormatter\SendStateFormatter.
 */

namespace Drupal\mailmute\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\String;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Formatter for the 'sendstate' entity field.
 *
 * @FieldFormatter(
 *   id = "sendstate",
 *   label = @Translation("Send state"),
 *   field_types = {
 *     "sendstate"
 *   }
 * )
 */
class SendStateFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $sendstates = \Drupal::service('plugin.manager.sendstate')->getDefinitions();
    $elements = array();

    foreach ($items as $delta => $item) {
      $info = $sendstates[$item->value];
      $elements[$delta] = array('#markup' => String::checkPlain($info['label']));
    }

    return $elements;
  }

}
