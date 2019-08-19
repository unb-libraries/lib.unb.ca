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

  /**
   * Create the default taxonomy terms for Library Departments.
   */
  public static function addDefaultDepartmentsTerms() {
    $config = \Drupal::config('lib_core.taxonomy.library.departments.default_terms');
    $dept_terms = $config->get('terms');
    foreach ($dept_terms as $term) {
      Term::create([
        'field_anchor' => $term['anchor'],
        'field_department_email' => $term['email'],
        'field_department_fax' => $term['fax'],
        'field_department_location' => $term['location'],
        'field_department_phone' => $term['phone'],
        'field_department_website' => $term['website'],
        'name' => $term['name'],
        'vid' => 'library_departments',
      ])
        ->save();
    }
  }

  /**
   * Create the default taxonomy terms for university faculties/programs.
   */
  public static function addDefaultFacultiesTerms() {
    $config = \Drupal::config('lib_core.taxonomy.faculties.default_terms');
    $faculty_terms = $config->get('terms');
    foreach ($faculty_terms as $term) {
      Term::create([
        'field_campus' => $term['campus'],
        'field_department_rep_email' => $term['rep_email'],
        'field_department_rep_name' => $term['rep_name'],
        'field_department_rep_phone' => $term['rep_phone'],
        'name' => $term['name'],
        'vid' => 'faculties',
      ])
        ->save();
    }
  }

}
