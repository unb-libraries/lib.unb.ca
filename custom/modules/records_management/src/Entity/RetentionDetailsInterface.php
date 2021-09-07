<?php

namespace Drupal\records_management\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Interface for "Retention details" entities.
 */
interface RetentionDetailsInterface extends ContentEntityInterface {

  const FIELD_TRIGGER = 'trigger';

  /**
   * Get the event triggering the retention.
   *
   * @return string
   *   A string, e.g. "End of fiscal year".
   */
  public function getTrigger();

}
