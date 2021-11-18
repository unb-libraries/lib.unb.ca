<?php

namespace Drupal\ior\Entity\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageTrait;
use Drupal\ior\Entity\ContestInterface;

/**
 * Storage handler for contest entities.
 */
class ContestStorage extends SqlContentEntityStorage implements ContestStorageInterface {

  use RevisionableEntityStorageTrait;

  /**
   * {@inheritDoc}
   */
  public function loadBySubmission($submission_id) {
    $contest_ids = $this
      ->getQuery()
      ->condition(ContestInterface::FIELD_SUBMISSIONS, $submission_id, 'CONTAINS')
      ->execute();
    if (!empty($contest_ids)) {
      return $this->load($contest_ids[array_keys($contest_ids)[0]]);
    }
    return NULL;
  }

}
