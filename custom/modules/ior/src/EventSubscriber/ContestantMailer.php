<?php

namespace Drupal\ior\EventSubscriber;

use Drupal\Core\Entity\EntityInterface;
use Drupal\custom_entity_events\Event\EntityEvent;
use Drupal\custom_entity_events\Event\EntityEvents;
use Drupal\custom_entity_mail\EventSubscriber\EntityEventTemplateMailer;

/**
 * Confirm a successful submission to the contestant.
 */
class ContestantMailer extends EntityEventTemplateMailer {

  /**
   * {@inheritDoc}
   */
  protected static function getSubscribedEventNames() {
    return [
      EntityEvents::CREATE,
      EntityEvents::UPDATE,
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function doOnEntityUpdate(EntityEvent $event) {
    $entity = $event->getEntity();
    $state = $entity->get('moderation_state')->value;
    $state_updated = $this->hasEntityModerationStateUpdated($entity);
    if ($state_updated && in_array($state, ['accepted', 'rejected'])) {
      parent::doOnEntityUpdate($event);
    }
  }

  /**
   * Whether the moderation state of the given entity has changed.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   An entity.
   *
   * @return bool
   *   TRUE if the moderation state of the given entity differs from the state
   *   of its preceding revision. FALSE if both are equal.
   */
  protected function hasEntityModerationStateUpdated(EntityInterface $entity) {
    $current_state = $entity->get('moderation_state')->value;
    $previous_revision = $entity->getPreviousRevision();
    $previous_state = $previous_revision
      ->get('moderation_state')->value;
    return $previous_state !== $current_state;
  }

  /**
   * {@inheritDoc}
   */
  protected function getRecipients(EntityEvent $event) {
    /** @var \Drupal\ior\Entity\SubmissionInterface $submission */
    $submission = $event->getEntity();
    return [$submission->getEmail()];
  }

  /**
   * {@inheritDoc}
   */
  protected function getSubjectTemplate(EntityInterface $entity, string $key) {
    return parent::getSubjectTemplate($entity, $this->buildKey($entity));
  }

  /**
   * {@inheritDoc}
   */
  protected function getBodyTemplate(EntityInterface $entity, string $key) {
    return parent::getBodyTemplate($entity, $this->buildKey($entity));
  }

  /**
   * Build the key that identifies subject/body templates.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return string
   *   A string.
   */
  protected function buildKey(EntityInterface $entity) {
    $moderation_state = $entity->get('moderation_state')->value;
    return "contestant.ior_submission.{$moderation_state}";
  }

}
