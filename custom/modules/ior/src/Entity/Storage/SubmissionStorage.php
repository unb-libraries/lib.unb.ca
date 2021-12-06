<?php

namespace Drupal\ior\Entity\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\ior\Entity\SubmissionInterface;

/**
 * Entity storage handler for ior_submission entities.
 */
class SubmissionStorage extends SqlContentEntityStorage implements SubmissionStorageInterface {

  /**
   * {@inheritDoc}
   */
  public function loadByContest($contest_id) {
    return $this->loadByProperties([
      SubmissionInterface::FIELD_CONTEST => $contest_id,
    ]);
  }

}
