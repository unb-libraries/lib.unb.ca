<?php

namespace Drupal\ior\Entity\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageTrait;

/**
 * Storage handler for contest entities.
 */
class ContestStorage extends SqlContentEntityStorage implements ContestStorageInterface {

  use RevisionableEntityStorageTrait;

  /**
   * The submission storage.
   *
   * @var \Drupal\ior\Entity\Storage\SubmissionStorageInterface
   */
  protected $submissionStorage;

  /**
   * Get the submission storage.
   *
   * @return \Drupal\ior\Entity\Storage\SubmissionStorageInterface
   *   An ior_submission entity storage handler.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function submissionStorage() {
    if (!isset($this->submissionStorage)) {
      $this->submissionStorage = $this->entityTypeManager
        ->getStorage('ior_submission');
    }
    return $this->submissionStorage;
  }

  /**
   * {@inheritDoc}
   */
  public function loadSubmissions($contest_id) {
    return $this->submissionStorage()
      ->loadByContest($contest_id);
  }

}
