<?php

namespace Drupal\ior\EventSubscriber;

use Drupal\Core\Entity\EntityInterface;
use Drupal\custom_entity_mail\EventSubscriber\EntityEventTemplateMailer;
use Drupal\custom_entity_events\Event\EntityEvent;
use Drupal\custom_entity_events\Event\EntityEvents;

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
    $moderation_state = $event->getEntity()->get('moderation_state')->value;
    if (in_array($moderation_state, ['accepted', 'rejected'])) {
      parent::doOnEntityUpdate($event);
    }
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
