<?php

namespace Drupal\ior\Entity\Storage;

use Drupal\Core\Entity\ContentEntityStorageInterface;

/**
 * Interface for storage handlers for "contest" entities.
 */
interface ContestStorageInterface extends ContentEntityStorageInterface {

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

}
