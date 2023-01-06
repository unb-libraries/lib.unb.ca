<?php

namespace Drupal\guides\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides route responses for course_link entities.
 */
class GuidesAccessController extends ControllerBase {

  /**
   * Grant editing access to any listed editor.
   *
   * @param \Drupal\Core\Entity\EntityInterface $guide
   *   An entity.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   A user.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function checkEditAccess(EntityInterface $guide, AccountInterface $account) {
    foreach ($guide->editors as $editor) {
      $user = $editor->entity->field_user->entity;
      if ($user->id() == $account->id()) {
        return AccessResult::allowed();
      }
    }

    return AccessResult::forbidden();
  }

}
