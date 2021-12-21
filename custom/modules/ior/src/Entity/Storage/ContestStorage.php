<?php

namespace Drupal\ior\Entity\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageTrait;

/**
 * Storage handler for contest entities.
 */
class ContestStorage extends SqlContentEntityStorage implements ContestStorageInterface {

  use RevisionableEntityStorageTrait;
  use SubmissionStorageTrait;

  /**
   * {@inheritDoc}
   */
  public function loadSubmissions($contest_id) {
    return $this->submissionStorage()
      ->loadByContest($contest_id);
  }

  /**
   * {@inheritDoc}
   */
  public function deleteSubmissions($contest_id) {
    return $this->submissionStorage()
      ->delete($this->loadSubmissions($contest_id));
  }

}
