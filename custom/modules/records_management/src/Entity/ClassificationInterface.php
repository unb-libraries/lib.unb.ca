<?php

namespace Drupal\records_management\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Interface for "Functional classification" entities.
 */
interface ClassificationInterface extends ContentEntityInterface {

  const FIELD_CODE = 'code';
  const FIELD_NAME = 'name';
  const FIELD_DESCRIPTION = 'description';

  /**
   * Get the classification code.
   *
   * @return string
   *   A two-letter string, e.g. "AD".
   */
  public function getCode();

  /**
   * Get the classification name.
   *
   * @return string
   *   A string, e.g. "Administration".
   */
  public function getName();

  /**
   * Get the classification description.
   *
   * @return string
   *   A (long) string.
   */
  public function getDescrition();

}
