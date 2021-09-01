<?php

namespace Drupal\records_management\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Interface for "Functional classification" entities.
 */
interface ClassificationInterface extends ContentEntityInterface {

  const FIELD_DESCRIPTION = 'description';

  /**
   * Get the classification description.
   *
   * @return string
   *   A string.
   */
  public function getDescrition();

}
