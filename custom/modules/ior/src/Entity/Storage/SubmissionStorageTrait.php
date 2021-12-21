<?php

namespace Drupal\ior\Entity\Storage;

/**
 * Inject the submission storage into other entity storage handlers.
 */
trait SubmissionStorageTrait {

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

}
