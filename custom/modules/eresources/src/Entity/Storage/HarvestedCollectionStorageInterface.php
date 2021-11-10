<?php

namespace Drupal\eresources\Entity\Storage;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Interface for harvested collection entity storage handlers.
 */
interface HarvestedCollectionStorageInterface extends ContentEntityStorageInterface {

  /**
   * Count all records in the collection.
   *
   * @param \Drupal\Core\Entity\EntityInterface $collection
   *   The collection.
   *
   * @return int
   *   The count.
   */
  public function getRecordCount(EntityInterface $collection);

}
