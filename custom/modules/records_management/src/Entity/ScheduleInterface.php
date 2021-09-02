<?php

namespace Drupal\records_management\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Interface for "Retention schedule" entities.
 *
 * @package Drupal\records_management\Entity
 */
interface ScheduleInterface extends ContentEntityInterface {

  const FIELD_NAME = 'name';
  const FIELD_NUMBER = 'number';
  const FIELD_CLASSIFICATION = 'classification';

  /**
   * Get the schedule name.
   *
   * @return string
   *   A string, e.g. "Journal Entries".
   */
  public function getName();

  /**
   * Get the schedule number.
   *
   * @return string
   *   A string, e.g. '1000' or '1000.01'.
   */
  public function getNumber();

  /**
   * Get the schedule classification.
   *
   * @return \Drupal\records_management\Entity\ClassificationInterface
   *   A classification entity.
   */
  public function getClassification();

}
