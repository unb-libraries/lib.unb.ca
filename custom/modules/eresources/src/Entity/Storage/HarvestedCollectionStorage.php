<?php

namespace Drupal\eresources\Entity\Storage;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * Storage handler for "Harvested Collection" entities.
 */
class HarvestedCollectionStorage extends SqlContentEntityStorage implements HarvestedCollectionStorageInterface {

  /**
   * {@inheritDoc}
   */
  public function getRecordCount(EntityInterface $collection) {
    $query = \Drupal::entityQuery('eresources_record');
    return $query->condition('collection_id', $collection->id())->count()->execute();
  }

}
