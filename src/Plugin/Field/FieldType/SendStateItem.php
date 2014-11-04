<?php
/**
 * @file
 * Contains \Drupal\mailmute\Plugin\Field\FieldType\SendStateItem.
 */

namespace Drupal\mailmute\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslationWrapper;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\OptionsProviderInterface;
use Drupal\Core\TypedData\TypedDataInterface;

/**
 * The 'sendstate' entity field type references a send state plugin.
 *
 * @FieldType(
 *   id = "sendstate",
 *   label = @Translation("Send state"),
 *   description = @Translation("An e-mail send state."),
 *   default_widget = "sendstate",
 *   default_formatter = "sendstate"
 * )
 */
class SendStateItem extends FieldItemBase implements OptionsProviderInterface {

  /**
   * Definitions of all send states.
   *
   * @var array[]
   *   Definition info arrays, keyed by plugin ID.
   */
  protected $sendstateDefinitions = array();

  /**
   * {@inheritdoc}
   */
  public function __construct(DataDefinitionInterface $definition, $name = NULL, TypedDataInterface $parent = NULL) {
    parent::__construct($definition, $name, $parent);
    $this->sendstateDefinitions = \Drupal::service('plugin.manager.sendstate')->getDefinitions();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslationWrapper('Send state plugin ID'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'value' => array(
          'type' => 'varchar',
          'length' => (int) $field_definition->getSetting('max_length'),
          'not null' => FALSE,
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleValues(AccountInterface $account = NULL) {
    // @todo Check permission.
    return array_keys($this->sendstateDefinitions);
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    // @todo Check permission.
    return array_map(function($definition) {
      return $definition['label'];
    }, $this->sendstateDefinitions);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableValues(AccountInterface $account = NULL) {
    // @todo Check permission.
    return $this->getPossibleValues();
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(AccountInterface $account = NULL) {
    // @todo Check permission.
    return $this->getPossibleOptions();
  }

}
