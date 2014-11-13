<?php
/**
 * @file
 * Contains \Drupal\mailmute\SendStateManager.
 */

namespace Drupal\mailmute;

use Drupal\Component\Plugin\FallbackPluginManagerInterface;
use Drupal\Component\Utility\String;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\mailmute\Plugin\Mailmute\SendState\SendStateInterface;

/**
 * Service for checking whether to suppress sending mail to some address.
 */
class SendStateManager extends DefaultPluginManager implements SendStateManagerInterface, FallbackPluginManagerInterface {

  /**
   * The entity manager, used for finding send state fields.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Lazy-loaded send states, keyed by address.
   *
   * @var \Drupal\mailmute\Plugin\Mailmute\SendState\SendStateInterface[]
   */
  protected $states;

  /**
   * Constructs a SendStateManager object.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler, EntityManagerInterface $entity_manager) {
    parent::__construct('Plugin/Mailmute/SendState', $namespaces, $module_handler, '\Drupal\mailmute\Plugin\Mailmute\SendState\SendStateInterface', '\Drupal\mailmute\Annotation\SendState');
    $this->setCacheBackend($cache_backend, 'mailmute_sendstate');
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getState($address) {
    $field = $this->getField($address);
    if (isset($field->plugin_id)) {
      $this->states[$address] = $this->createInstance($field->plugin_id, (array) $field->configuration);
      return $this->states[$address];
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function save($address) {
    $state = $this->states[$address];
    $this->transition($address, $state->getPluginId(), $state->getConfiguration());
  }

  /**
   * {@inheritdoc}
   */
  public function transition($address, $plugin_id, array $configuration = array()) {
    if ($field = $this->getField($address)) {
      if ($this->hasDefinition($plugin_id)) {
        $field->plugin_id = $plugin_id;
        $field->configuration = $configuration;
        // @todo Can we save field directly, without dealing with the entity?
        //   Like, what if the entity was changed somewhere else after we loaded
        //   the field?
        $field->getEntity()->save();
      }
      else {
        throw new \InvalidArgumentException(String::format('Unknown state "@state"', ['@state' => $plugin_id]));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isManaged($address) {
    return $this->getField($address);
  }

  /**
   * Find and return the send state field for the given email address.
   *
   * @param string $email
   *   An email address.
   *
   * @return \Drupal\Core\Field\FieldItemListInterface
   *   The send state field, or NULL if not found.
   *
   * @todo Lazy-load the field?
   */
  protected function getField($email) {
    foreach ($this->entityManager->getFieldMap() as $entity_type => $fields) {

      // Both users and Simplenews subscribers use 'mail' for email field name.
      if (isset($fields['field_sendstate']) && isset($fields['mail'])) {

        // Get the entity for the given email.
        $entities = $this->entityManager->getStorage($entity_type)->loadByProperties(array(
          'mail' => $email,
        ));
        $entity = reset($entities);

        // Return the send state field.
        if ($entity) {
          return $entity->field_sendstate;
        }
      }
    }

    // There may be multiple entities with the given email. Return NULL only if
    // none of them has the send state field.
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getFallbackPluginId($plugin_id, array $configuration = array()) {
    return 'send';
  }
}
