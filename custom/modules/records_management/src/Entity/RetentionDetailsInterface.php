<?php

namespace Drupal\records_management\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Interface for "Retention details" entities.
 */
interface RetentionDetailsInterface extends ContentEntityInterface {

  const FIELD_TRIGGER = 'trigger';
  const FIELD_DURATION_OFFICE = 'active';
  const FIELD_DURATION_STORAGE = 'semi_active';
  const FIELD_DISPOSITION = 'disposition';

  /**
   * Get the event triggering the retention.
   *
   * @return string
   *   A string, e.g. "FY" (= End of fiscal year).
   */
  public function getTrigger();

  /**
   * Get the time spent in office filling space.
   *
   * @return string
   *   A string.
   */
  public function getDurationOffice();

  /**
   * Get the time spent in storage space.
   *
   * @return string
   *   A string.
   */
  public function getDurationStorage();

  /**
   * Get the method of disposal.
   *
   * @return string
   *   A string, e.g. "SD" (= Secure Destruction).
   */
  public function getDisposition();

}
