<?php

namespace Drupal\ior_awards\Entity\Storage;

use Drupal\Core\Entity\ContentEntityStorageInterface;

/**
 * Interface for IOR award entity storage handlers.
 */
interface AwardStorageInterface extends ContentEntityStorageInterface {

  /**
   * Load all awards that have been awarded to submissions to the given contest.
   *
   * @return \Drupal\ior_awards\Entity\AwardInterface[]
   *   An array of award entities.
   */
  public function loadByContest($contest_id);

  /**
   * Load all awards that have been awarded to the submission with the given ID.
   *
   * @param int|string $submission_id
   *   A submission ID.
   *
   * @return \Drupal\ior_awards\Entity\AwardInterface[]
   *   An array of IOR award entities.
   */
  public function loadBySubmission($submission_id);

}
