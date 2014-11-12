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
    /** @var \Drupal\mailmute\Plugin\Mailmute\SendState\SendStateInterface $plugin */
    $plugin = $items->first()->getPlugin();

    return array(
      'value' => array(
        '#type' => 'select',
        '#options' => $this->getOptions($items->getEntity()),
        '#default_value' => $plugin->getPluginId(),
      ),
      'data' => $plugin->form(),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function sanitizeLabel(&$label) {
    // Select form inputs allow unencoded HTML entities, but no HTML tags.
    $label = String::decodeEntities(strip_tags($label));
  }

}
