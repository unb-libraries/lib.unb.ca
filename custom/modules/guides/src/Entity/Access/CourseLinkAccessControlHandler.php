<?php

namespace Drupal\guides\Entity\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\custom_entity\Entity\Access\EntityAccessControlHandler;

/**
 * Access control handler for "course_link" entities.
 */
class CourseLinkAccessControlHandler extends EntityAccessControlHandler {

  /**
   * Grant create access to any listed editor.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user for which to check access.
   * @param array $context
   *   An array of key-value pairs to pass additional context when needed.
   * @param string|null $entity_bundle
   *   (optional) The bundle of the entity. Required if the entity supports
   *   bundles, defaults to NULL otherwise.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    if (!$this->hasCourseLinkAccess($account)) {
      return AccessResult::allowedIf($account->hasPermission('administer course_link entities'));
    }
    return parent::checkCreateAccess($account, $context, $entity_bundle);
  }

  /**
   * Grant update access to any listed editor.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   An entity.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   A user.
   *
   * @return bool
   *   TRUE if user is listed, FALSE otherwise.
   */
  public function hasEntityUpdateAccess(EntityInterface $entity, AccountInterface $account) {
    return $this->hasCourseLinkAccess($account);
  }

  /**
   * Grant delete access to any listed editor.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   An entity.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   A user.
   *
   * @return bool
   *   TRUE if user is listed, FALSE otherwise.
   */
  public function hasEntityDeleteAccess(EntityInterface $entity, AccountInterface $account) {
    return $this->hasCourseLinkAccess($account);
  }

  /**
   * Grant access to any listed editor.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   A user.
   *
   * @return bool
   *   TRUE if user is listed, FALSE otherwise.
   */
  private function hasCourseLinkAccess(AccountInterface $account) {
    $entity = \Drupal::routeMatch()->getParameter('guide');
    if (!is_object($entity)) {
      $entity = \Drupal::entityTypeManager()->getStorage('guide')->load((int) $entity);
    }

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

}
