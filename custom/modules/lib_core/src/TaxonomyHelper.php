<?php

namespace Drupal\lib_core;

use Drupal\taxonomy\Entity\Term;

/**
 * Defines an object to help with taxonomy operations.
 */
class TaxonomyHelper {

  /**
   * Create the default taxonomy terms for Categories.
   */
  public static function addDefaultCategoriesTerms() {
    $config = \Drupal::config('lib_core.taxonomy.categories.default_terms');
    $categories_terms = $config->get('terms');
    foreach ($categories_terms as $term) {
      Term::create([
        'name' => $term['name'],
        'vid' => 'categories',
      ])
        ->save();
    }
  }

}
