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
  protected function getRecipients(EntityEvent $event) {
    /** @var \Drupal\ior\Entity\SubmissionInterface $submission */
    $submission = $event->getEntity();
    return [$submission->getEmail()];
  }

  /**
   * {@inheritDoc}
   */
  protected function getSubjectTemplate(EntityInterface $entity, string $key) {
    if ($path = parent::getSubjectTemplate($entity, "contestant.{$key}")) {
      return $path;
    }
    return parent::getSubjectTemplate($entity, $key);
  }

  /**
   * {@inheritDoc}
   */
  protected function getBodyTemplate(EntityInterface $entity, string $key) {
    if ($path = parent::getBodyTemplate($entity, "contestant.{$key}")) {
      return $path;
    }
    return parent::getBodyTemplate($entity, $key);
  }

}
