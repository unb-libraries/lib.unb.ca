<?php

namespace Drupal\ior\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Interface for "submission" entities.
 */
interface SubmissionInterface extends ContentEntityInterface, EntityPublishedInterface {

  const FIELD_TITLE = 'field_title';

  /**
   * Get the title.
   *
   * @return string
   *   A string.
   */
  public function getTitle();

}
