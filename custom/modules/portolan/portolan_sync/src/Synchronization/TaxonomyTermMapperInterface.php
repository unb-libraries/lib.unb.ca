<?php

namespace Drupal\portolan_sync\Synchronization;

/**
 * Interface for taxonomy term mappers.
 *
 * @package Drupal\portolan_sync\Synchronization
 */
interface TaxonomyTermMapperInterface {

  /**
   * Map the given value to a taxonomy term of the given vocabulary ID.
   *
   * @param string $vid
   *   A vocabulary ID string.
   * @param string $value
   *   A string.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   A taxonomy term entity. NULL if an error occurred.
   */
  public function getTerm(string $vid, string $value);

}
