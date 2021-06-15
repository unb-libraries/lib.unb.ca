<?php

namespace Drupal\portolan\Entity\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access control handler for portolan_record entities.
 *
 * @package Drupal\portolan\Entity\Access
 */
class PortolanRecordAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritDoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    $access = parent::checkAccess($entity, $operation, $account);
    if (!$access->isForbidden()) {
      $access = AccessResult::allowedIfHasPermission($account, "$operation {$entity->getEntityTypeId()} entities");
    }
    return $access;
  }

}
