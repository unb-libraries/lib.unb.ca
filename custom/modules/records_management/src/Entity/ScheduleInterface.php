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

  /**
   * Get the schedule name.
   *
   * @return string
   *   A string, e.g. "Journal Entries".
   */
  public function getName();

}
