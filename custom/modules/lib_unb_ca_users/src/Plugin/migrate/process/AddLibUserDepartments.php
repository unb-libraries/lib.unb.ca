<?php

namespace Drupal\lib_unb_ca_users\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\taxonomy\Entity\Term;

/**
 * Create/queue library department entity references for a target user entity.
 *
 * @MigrateProcessPlugin(
 *   id = "add_lib_user_departments"
 * )
 *
 * To invoke:
 *
 * @code
 * field_department:
 *   plugin: add_lib_user_departments
 *   source:
 *     - departments_are_head
 *     - departments_regular_member
 * @endcode
 */
class AddLibUserDepartments extends ProcessPluginBase {

  const DEPARTMENT_DELIMITER = '|';
  const DEPARTMENT_VID = 'library_departments';

  /**
   * The departments to add the user to.
   *
   * @var int[]
   */
  private $curDepartments = [];

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $head_departments = $value[0];
    $member_departments = $value[1];

    $this->addDepartment($head_departments, TRUE);
    $this->addDepartment($member_departments, FALSE);

    return $this->curDepartments;
  }

  /**
   * Add departments to the target list.
   *
   * @param string $departments
   *   The names of the departments, multiple separated by DEPARTMENT_DELIMITER.
   * @param bool $is_head
   *   TRUE if the user is the head of all departments in list, FALSE otherwise.
   */
  private function addDepartment($departments, $is_head = FALSE) {
    foreach (explode(self::DEPARTMENT_DELIMITER, $departments) as $department) {
      $term = $this->getCreateDepartmentTerm($department);
      if (!empty($term) && is_object($term)) {
        $this->curDepartments[] = $term->id();
      }
    }
  }

  /**
   * Get a department Term entity from a name, creating it if it does not exist.
   *
   * @param string $name
   *   The name of the department.
   *
   * @return \Drupal\taxonomy\Entity\Term
   *   The first Term entity that matches the name, NULL otherwise.
   */
  private function getCreateDepartmentTerm($name) {
    $category_id = NULL;
    if (!empty($name)) {
      $name_tid = $this->taxTermExists($name, 'name', self::DEPARTMENT_VID);
      if (!empty($name_tid)) {
        $term = Term::load($name_tid);
      }
      else {
        $term = Term::create([
          'vid' => self::DEPARTMENT_VID,
          'name' => $name,
        ]);
        $term->save();
      }
      return $term;
    }
    return NULL;
  }

  /**
   * Check if a taxonomy term exists in a vocabulary based on a field value.
   *
   * @param string $value
   *   The name of the term.
   * @param string $field
   *   The field to match when validating.
   * @param string $vocabulary
   *   The vid to match.
   *
   * @return mixed
   *   Contains an INT of the tid if exists, FALSE otherwise.
   */
  public function taxTermExists($value, $field, $vocabulary) {
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $vocabulary);
    $query->condition($field, $value);
    $tids = $query->execute();
    if (!empty($tids)) {
      foreach ($tids as $tid) {
        return $tid;
      }
    }
    return FALSE;
  }

}
