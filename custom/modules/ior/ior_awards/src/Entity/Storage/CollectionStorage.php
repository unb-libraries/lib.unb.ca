<?php

namespace Drupal\ior_awards\Entity\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\ior\Entity\Storage\SubmissionStorageTrait;

/**
 * Storage handler for IOR collection entities.
 */
class CollectionStorage extends SqlContentEntityStorage implements CollectionStorageInterface {

  use SubmissionStorageTrait;

  /**
   * {@inheritDoc}
   */
  public function loadByContest($contest_id, array $options = []) {
    $collection_ids = [];

    $submissions = $this->submissionStorage()->loadByContest($contest_id, $options);
    foreach ($submissions as $submission) {
      $collections = $submission->get('field_collections')->referencedEntities();
      foreach ($collections as $collection) {
        $collection_ids[] = $collection->id();
      }
    }

    return $this->loadMultiple(array_unique($collection_ids));
  }

}
