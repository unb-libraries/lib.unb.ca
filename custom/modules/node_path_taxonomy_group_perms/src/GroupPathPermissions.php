<?php

namespace Drupal\node_path_taxonomy_group_perms;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node_path_taxonomy\Entity\NodeTaxonomyPath;
use Drupal\node_path_taxonomy\Entity\NodeTaxonomyPathRelationship;
use Drupal\simple_group\Entity\SimpleGroup;
use Drupal\user\Entity\User;

/**
 * Defines the permissions related to group path.
 *
 * @ingroup node_path_taxonomy_group_perms
 */
class GroupPathPermissions {

  /**
   * Create some default paths for simple group permissions.
   */
  public static function userHasGroupNodePermission($user, $node, $op) {
    $uid = $user->id();
    $user = User::load($uid);

    switch ($op) {
      case 'view':
        // We are not concerned with viewing.
        return $access = AccessResult::neutral();

      default:
        // All others : create, delete, update.
        return self::userHasAccessToNode($user, $node);
    }
  }

  /**
   * Determine if a user has access to edit a node configured for tax paths.
   */
  public static function userhasAccessToNode($user, $node) {
    // If this is a new node, check user access to any path term associated.
    if ($node->isNew()) {
      $type = $node->bundle();
      return self::userHasAnyAccessInType($user, $type);
    }

    // Otherwise, check for specific path.
    $user_path_accesses = self::getUserPathPermissions($user, $node->bundle());
    $node_path = NodeTaxonomyPath::getNodePathTerm($node);
    foreach ($user_path_accesses as $user_path_access) {
      if ($node_path->id() == $user_path_access->id()) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Determine if a user has access to create a node configured for tax paths.
   */
  public static function userHasAnyAccessInType($user, $node_type) {
    return (!empty(self::getUserPathPermissions($user, $node_type)));
  }

  /**
   * Get path terms that a user has access to for this node type.
   */
  public static function getUserPathPermissions($user, $node_type) {
    $groups = SimpleGroup::getUserGroups($user);
    $paths = [];
    $valid_vid = NodeTaxonomyPathRelationship::loadByNodeType($node_type);

    foreach ($groups as $group) {
      $path_terms = $group->get('perm_content_paths')->referencedEntities();
      foreach ($path_terms as $path_term) {
        if ($path_term->bundle() == $valid_vid) {
          $paths[] = $path_term;
          $tree_descendants = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($valid_vid, $path_term->id(), NULL, TRUE);
          foreach ($tree_descendants as $tree_descendant) {
            $paths[] = $tree_descendant;
          }
        }
      }
    }
    return $paths;
  }

  /**
   * Validate the form submit : user has access to publication path.
   */
  public static function validateNodeForm(array &$form, FormStateInterface $form_state) {
    $node = $form_state->getFormObject()->getEntity();
    $node_type = $node->bundle();
    $uid = \Drupal::currentUser()->id();
    if ($uid != 1) {
      $user = User::load($uid);
      $publication_path_tid = $form_state->getValue('publication_path');
      $path_permission_terms = GroupPathPermissions::getUserPathPermissions($user, $node_type);
      foreach ($path_permission_terms as $path_permission_term) {
        if ($path_permission_term->id() == $publication_path_tid) {
          return;
        }
      }
      $form_state->setErrorByName('publication_path', t('You do not have permission to publish at the selected path.'));
    }
  }

  /**
   * Get all nids a user has permission to edit based on group permissions.
   */
  public static function getCurrentUserEditPermissionsNids() {
    $nids = [];
    $uid = \Drupal::currentUser()->id();
    $user = User::load($uid);

    // User path content permissions.
    $controlled_types = NodeTaxonomyPathRelationship::getConfiguredNodeTypes();
    foreach ($controlled_types as $controlled_type) {
      $permission_terms = GroupPathPermissions::getUserPathPermissions($user, $controlled_type);
      $user_page_nids = NodeTaxonomyPath::getNidsByPathTerms($permission_terms);
      $nids = array_merge($nids, $user_page_nids);
    }

    return $nids;
  }

}
