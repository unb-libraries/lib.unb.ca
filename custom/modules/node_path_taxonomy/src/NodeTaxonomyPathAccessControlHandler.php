<?php

namespace Drupal\node_path_taxonomy;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Node taxonomy path entity.
 *
 * @see \Drupal\node_path_taxonomy\Entity\NodeTaxonomyPath.
 */
class NodeTaxonomyPathAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\node_path_taxonomy\Entity\NodeTaxonomyPathInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished node taxonomy path entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published node taxonomy path entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit node taxonomy path entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete node taxonomy path entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add node taxonomy path entities');
  }

}
