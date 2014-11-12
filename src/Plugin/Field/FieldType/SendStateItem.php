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

    $properties['data'] = DataDefinition::create('map')
      ->setLabel(new TranslationWrapper('Additional state data'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        // @todo Change to "plugin" or something else more specific?
        'value' => array(
          'type' => 'varchar',
          'length' => (int) $field_definition->getSetting('max_length'),
          'not null' => TRUE,
        ),
        // The "data" column stores the plugin configuration, i.e. additional
        // information about the state.
        'data' => array(
          'type' => 'text',
          'not null' => FALSE,
        ),
      ),
    );
  }

  /**
   * Returns an instance of the plugin, with configuration from field values.
   *
   * @return \Drupal\mailmute\Plugin\Mailmute\SendState\SendStateInterface
   *   The send state plugin referenced by the value of this field.
   */
  public function getPlugin() {
    return \Drupal::service('plugin.manager.sendstate')->createInstance($this->value, $this->data ?: array());
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleValues(AccountInterface $account = NULL) {
    // Return the plugin IDs of all states.
    return array_keys($this->sendstateDefinitions);
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    // Return the labels of all states.
    return array_map(function($definition) {
      return $definition['label'];
    }, $this->sendstateDefinitions);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableValues(AccountInterface $account = NULL) {
    // Filter states by access and return the plugin IDs.
    return array_keys($this->getSettableSendStates($account));
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(AccountInterface $account = NULL) {
    // Filter states by access and return the labels.
    return array_map(function($definition) {
      return $definition['label'];
    }, $this->getSettableSendStates($account));
  }

  /**
   * Returns the send state definitions to which a given account has access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account.
   *
   * @return array
   *   A subset of the state definitions.
   */
  protected function getSettableSendStates(AccountInterface $account) {
    return array_filter(
      $this->sendstateDefinitions,
      function($sendstate) use ($account) {
        return $this->hasChangeAccess($account, $sendstate);
      }
    );
  }

  /**
   * Check that an account has access to change to a given send state.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account.
   * @param array $sendstate
   *   The send state plugin definition.
   *
   * @return bool
   *   Whether the account may set the send state.
   */
  protected function hasChangeAccess(AccountInterface $account, array $sendstate) {
    // Keeping the current value is always allowed.
    if (isset($this->value) && $this->value == $sendstate['id']) {
      return TRUE;
    }

    // The admin permission allows setting any state.
    if ($account->hasPermission('administer send state')) {
      return TRUE;
    }

    // At least the "change own send state" permission is required.
    return isset($account) && empty($sendstate['admin']) && $account->hasPermission('change own send state');
  }

}
