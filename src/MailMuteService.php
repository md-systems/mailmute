<?php
/**
 * @file
 * Contains \Drupal\mailmute\MailMuteService.
 */

namespace Drupal\mailmute;

use Drupal\Core\Entity\EntityManagerInterface;

/**
 * Service for checking whether to suppress sending mail to some address.
 */
class MailMuteService  {

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
   * Constructs a MailMuteService object.
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * Check if the send state is 'Mute' for any entity having this email.
   *
   * @param string $email
   *   The email address that a mail is being sent to.
   *
   * @return bool
   *   TRUE if any entity having the given email also has a send state field
   *   with a 'Mute' value; otherwise FALSE.
   */
  public function isMutedFor($email) {
    // @todo Extract email from strings like "Foo Bar <foo.bar@example.com>".
    // @todo Handle multiple recipients.
    foreach ($this->entityManager->getFieldMap() as $entity_type => $fields) {
      if (isset($fields['field_sendstate']) && isset($fields['mail'])) {
        $entities = $this->entityManager->getStorage($entity_type)->loadByProperties(array(
          'mail' => $email,
        ));
        $entity = reset($entities);
        $send_state = $entity->field_sendstate->value;
        if ($send_state != static::STATE_SEND) {
          return TRUE;
        }
      }
    }
    return FALSE;
  }

}
