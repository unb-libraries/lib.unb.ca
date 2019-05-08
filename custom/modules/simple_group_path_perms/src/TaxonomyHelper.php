<?php

namespace Drupal\simple_group_path_perms;

use Drupal\taxonomy\Entity\Term;

/**
 * Defines an object to help with taxonomy operations.
 */
class TaxonomyHelper {

  /**
   * Create some default paths for simple group permissions.
   */
  public static function addDefaultPaths() {
    $config = \Drupal::config('simple_group_path_perms.taxonomy.simple_group_paths.default_terms');
    $paths = $config->get('default_paths');
    foreach ($paths as $parent_path => $sub_paths) {
      $parent_term = Term::create(
        [
          'parent' => [],
          'name' => $parent_path,
          'vid' => SIMPLE_GROUP_PATH_PERMS_TAXONOMY_VID,
        ]
      );
      $parent_term->save();
      if (!empty($sub_paths)) {
        foreach ($sub_paths as $sub_path) {
          Term::create(
            [
              'parent' => [$parent_term->id()],
              'name' => $sub_path,
              'vid' => SIMPLE_GROUP_PATH_PERMS_TAXONOMY_VID,
            ]
          )->save();
        }
      }
      unset($parent_term);
    }
  }

}
