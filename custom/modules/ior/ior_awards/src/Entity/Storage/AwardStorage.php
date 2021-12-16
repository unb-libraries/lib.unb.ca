<?php

namespace Drupal\ior_awards\Entity\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * Storage handler for award entities.
 */
class AwardStorage extends SqlContentEntityStorage implements AwardStorageInterface {

  /**
   * The submission entity storage.
   *
   * @var \Drupal\ior\Entity\Storage\SubmissionStorageInterface
   */
  protected $submissionStorage;

  /**
   * Get the submission entity storage.
   *
   * @return \Drupal\ior\Entity\Storage\SubmissionStorageInterface
   *   A storage handler for IOR submission entities.
   */
  protected function submissionStorage() {
    if (!isset($this->submissionStorage)) {
      $this->submissionStorage = $this
        ->entityTypeManager
        ->getStorage('ior_submission');
    }
    return $this->submissionStorage;
  }

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
