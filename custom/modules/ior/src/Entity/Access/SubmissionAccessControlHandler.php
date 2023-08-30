<?php

namespace Drupal\ior\Entity\Access;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
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
  protected function hasEntityTypePermission(string $operation, AccountInterface $account, EntityTypeInterface $entity_type, ConfigEntityInterface $bundle = NULL) {
    [$moderation_state, $operation] = explode('.', $operation);
    if (!$access = parent::hasEntityTypePermission($operation, $account, $entity_type, $bundle)) {
      $access = parent::hasEntityTypePermission("{$operation} {$moderation_state}", $account, $entity_type, $bundle);
    }
    return $access;
  }

}
