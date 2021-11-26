<?php

namespace Drupal\ior\Entity\Access;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\custom_entity\Entity\Access\EntityAccessControlHandler;

/**
 * Access control handler for "ior_submission" entities.
 */
class SubmissionAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritDoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    $moderation_state = $entity->get('moderation_state')->value;
    return parent::checkAccess($entity, "{$moderation_state}.{$operation}", $account);
  }

  /**
   * {@inheritDoc}
   */
  protected function hasEntityTypePermission(EntityTypeInterface $entity_type, string $operation, AccountInterface $account) {
    list($moderation_state, $operation) = explode('.', $operation);
    if (!$access = parent::hasEntityTypePermission($entity_type, $operation, $account)) {
      $access = parent::hasEntityTypePermission($entity_type, "{$operation} {$moderation_state}", $account);
    }
    return $access;
  }

}
