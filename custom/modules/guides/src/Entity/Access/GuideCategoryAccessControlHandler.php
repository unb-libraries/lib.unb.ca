<?php

namespace Drupal\guides\Entity\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\custom_entity\Entity\Access\EntityAccessControlHandler;

/**
 * Access control handler for "guide_category" entities.
 */
class GuideCategoryAccessControlHandler extends EntityAccessControlHandler {

  /**
   * Grant editing access to any contact listed on the category.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   An entity.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   A user.
   *
   * @return bool
   *   TRUE if user is listed, FALSE otherwise.
   */
  protected function hasEntityUpdateAccess(EntityInterface $entity, AccountInterface $account) {
    $hasAccess = FALSE;
    foreach ($entity->editors as $editor) {
      $user = $editor->entity->field_user->entity;
      if ($user->id() == $account->id()) {
        $hasAccess = TRUE;
        break;
      }
    }
    return $hasAccess;
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    $status = $entity->isPublished();

    if ($operation === 'view' && !$status && !$account->hasPermission('view unpublished guide_categories entities')) {
      return AccessResult::forbidden()->addCacheableDependency($entity);
    }

    return parent::checkAccess($entity, $operation, $account);
  }

}
