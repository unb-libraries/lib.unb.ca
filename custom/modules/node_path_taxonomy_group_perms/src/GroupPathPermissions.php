<?php

namespace Drupal\node_path_taxonomy_group_perms;

use Drupal\Core\Access\AccessResult;

/**
 * Defines the permissions related to group path.
 *
 * @ingroup node_path_taxonomy_group_perms
 */
class GroupPathPermissions {

  /**
   * Create some default paths for simple group permissions.
   */
  public static function userHasGroupNodeEditPermission($user, $node, $op) {
    $type = $node->bundle();

    switch ($op) {
      case 'create':
        // User can create a node of this type if access to write in any path.
        break;

      default:
        // All others : delete, update. Does the user have the specific path?
        break;
    }

  }

}
