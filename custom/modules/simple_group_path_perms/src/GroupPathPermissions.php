<?php

namespace Drupal\simple_group_path_perms;

/**
 * Defines the permissions related to group path.
 *
 * @ingroup simple_group_path_perms
 */
class GroupPathPermissions {

  /**
   * Create some default paths for simple group permissions.
   */
  public static function userHasGroupNodeEditPermission($user, $node) {
    return TRUE;
  }

}
