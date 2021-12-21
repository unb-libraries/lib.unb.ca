<?php

namespace Drupal\ior\EventSubscriber;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\custom_entity_mail\EventSubscriber\EntityEventTemplateMailer;
use Drupal\custom_entity_events\Event\EntityEvent;
use Drupal\custom_entity_events\Event\EntityEvents;
use Drupal\user\UserInterface;
use Drupal\user\UserStorageInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Confirm a successful submission to the contest manager(s).
 */
class ContestManagerMailer extends EntityEventTemplateMailer {

  /**
   * The user storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * Get the user storage.
   *
   * @return \Drupal\user\UserStorageInterface
   *   A storage handler for user entities.
   */
  protected function userStorage() {
    return $this->userStorage;
  }

  /**
   * Create a new ContestManagerMailer instance.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   A mail manager instance.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   * @param string $entity_type_id
   *   A string.
   * @param \Drupal\user\UserStorageInterface $user_storage
   *   A storage handler for user entities.
   */
  public function __construct(MailManagerInterface $mail_manager, Request $request, string $entity_type_id, UserStorageInterface $user_storage) {
    parent::__construct($mail_manager, $request, $entity_type_id);
    $this->userStorage = $user_storage;
  }

  /**
   * {@inheritDoc}
   */
  protected static function getSubscribedEventNames() {
    return [
      EntityEvents::CREATE,
    ];
  }

  /**
   * {@inheritDoc}
   */
  protected function getRecipients(EntityEvent $event) {
    return array_map(function (UserInterface $contest_manager) {
      return $contest_manager->getEmail();
    }, $this->loadContestManagers());
  }

  /**
   * Load all users with the "IOR Contest Manager" role.
   *
   * @return array|\Drupal\Core\Entity\EntityInterface[]
   *   An array of user entities.
   */
  protected function loadContestManagers() {
    $user_ids = $this
      ->userStorage()
      ->getQuery()
      ->condition('roles', 'ior_manager', 'CONTAINS')
      ->execute();

    return $this
      ->userStorage()
      ->loadMultiple($user_ids);
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
    return "contest_manager.ior_submission.created";
  }

  /**
   * {@inheritDoc}
   */
  protected function getBodyContext(EntityInterface $entity, string $key) {
    $context = parent::getBodyContext($entity, $key);

    $review_url = $this->getEntityUrlBase() . $entity->toUrl('review-form')->toString();
    $context["{$entity->getEntityTypeId()}_review_url"] = $review_url;

    return $context;
  }

}
