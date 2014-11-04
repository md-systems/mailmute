<?php
/**
 * @file
 * Contains \Drupal\mailmute\Plugin\Field\FieldWidget\SendStateWidget.
 */

namespace Drupal\mailmute\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\String;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsWidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Select widget for the 'sendstate' entity field.
 *
 * @FieldWidget(
 *   id = "sendstate",
 *   label = @Translation("Send state"),
 *   field_types = {
 *     "sendstate"
 *   },
 *   multiple_values = TRUE
 * )
 */
class SendStateWidget extends OptionsWidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $default_value = $items->getFieldDefinition()->getDefaultValue($items->getEntity())[0];
    $element += array(
      '#type' => 'select',
      '#options' => $this->getOptions($items->getEntity()),
      '#default_value' => $this->getSelectedOptions($items, $delta) ?: $default_value,
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  protected function sanitizeLabel(&$label) {
    // Select form inputs allow unencoded HTML entities, but no HTML tags.
    $label = String::decodeEntities(strip_tags($label));
  }

}
