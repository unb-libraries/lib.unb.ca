<?php

namespace Drupal\simple_group;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the Simple group entity.
 *
 * @see \Drupal\simple_group\Entity\SimpleGroup.
 */
class SimpleGroupAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\simple_group\Entity\SimpleGroupInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished simple group entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published simple group entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit simple group entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete simple group entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add simple group entities');
  }

}
