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
  const FIELD_OOPR = 'oopr';
  const FIELD_PURPOSE = 'purpose';
  const FIELD_SUMMARY = 'summary';
  const FIELD_RATIONALE = 'rationale';
  const FIELD_VITAL = 'is_vital';

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

  /**
   * Get the office of primary responsibility.
   *
   * @return string
   *   A string, e.g. "Financial Services".
   */
  public function getOfficeOfPrimaryResponsibility();

  /**
   * Get the the description of the record's purpose.
   *
   * @return string
   *   A string.
   */
  public function getPurpose();

  /**
   * Get the summary of the record's content.
   *
   * @return string
   *   A string.
   */
  public function getSummary();

  /**
   * Get the retention rationale.
   *
   * @return string
   *   A string.
   */
  public function getRetentionRationale();

  /**
   * Whether the record contains "vital" information.
   *
   * @return bool
   *   TRUE if the record contains "vital" information. FALSE otherwise.
   */
  public function isVital();

}
