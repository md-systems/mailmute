<?php
/**
 * @file
 * Contains \Drupal\mailmute\SendStateManager.
 */

namespace Drupal\mailmute;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Service for checking whether to suppress sending mail to some address.
 *
 * @todo Rename to SendStateManager
 */
class SendStateManager extends DefaultPluginManager implements SendStateManagerInterface {

  /**
   * The value representing the 'Send' send state.
   */
  const STATE_SEND = 0;

  /**
   * The value representing the 'Mute' send state.
   */
  const STATE_MUTE = 1;

  /**
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * @var \Drupal\mailmute\Plugin\Mailmute\SendState\SendStateManager
   */
  protected $sendstateManager;

  /**
   * Constructs a SendStateManager object.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler, EntityManagerInterface $entity_manager) {
    parent::__construct('Plugin/Mailmute/SendState', $namespaces, $module_handler, '\Drupal\mailmute\SendStateInterface', '\Drupal\mailmute\Annotation\SendState');
    $this->setCacheBackend($cache_backend, 'mailmute_sendstate');
    $this->entityManager = $entity_manager;
  }

  /**
   * Get the send state for a given email.
   *
   * @param string $address
   *   The email address that a mail is being sent to.
   *
   * @return \Drupal\mailmute\SendStateInterface
   *   The current send state of the given email.
   */
  public function getState($address) {
    // @todo Handle multiple recipients.
    if ($field = $this->getField($address)) {
      return $this->createInstance($field->value);
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setState($address, SendStateInterface $state) {
    if ($field = $this->getField($address)) {
      $field->setValue($state);
      $field->getEntity()->save();
    }
  }

  /**
   * Find and return the send state field for the given email address.
   *
   * @param string $email
   *   An email address.
   *
   * @return \Drupal\Core\Field\FieldItemListInterface
   *   The send state field, or NULL if not found.
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

}
