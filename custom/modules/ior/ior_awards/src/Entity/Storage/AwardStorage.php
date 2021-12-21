<?php

namespace Drupal\ior_awards\Entity\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\ior\Entity\Storage\SubmissionStorageTrait;

/**
 * Storage handler for award entities.
 */
class AwardStorage extends SqlContentEntityStorage implements AwardStorageInterface {

  use SubmissionStorageTrait;

  /**
   * {@inheritDoc}
   */
  public function loadByContest($contest_id, array $options = []) {
    $submissions = $this->submissionStorage()
      ->loadByContest($contest_id, $options);
    return $this->loadMultiple($this
      ->getQuery()
      ->condition('field_submission', array_keys($submissions), 'IN')
      ->execute());
  }

  /**
   * {@inheritDoc}
   */
  public function loadBySubmission($submission_id, array $options = []) {
    return $this->loadByProperties([
      'field_submission' => $submission_id,
    ]);
  }

}
