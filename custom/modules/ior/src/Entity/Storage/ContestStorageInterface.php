<?php

namespace Drupal\ior\Entity\Storage;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageInterface;

/**
 * Interface for storage handlers for "contest" entities.
 */
interface ContestStorageInterface extends ContentEntityStorageInterface, RevisionableEntityStorageInterface {

  /**
   * Load submission entities for the given contest ID.
   *
   * @param int|string $contest_id
   *   The contest ID.
   *
   * @return \Drupal\ior\Entity\SubmissionInterface[]
   *   An array of submission entities.
   */
  public function loadSubmissions($contest_id);

  /**
   * Delete submission entities for the given contest ID.
   *
   * @param int|string $contest_id
   *   The contest ID.
   */
  public function deleteSubmissions($contest_id);

}
