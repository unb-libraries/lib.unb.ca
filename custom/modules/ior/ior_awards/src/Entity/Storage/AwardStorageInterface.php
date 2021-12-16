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
   * @param int|string $contest_id
   *   A contest ID.
   * @param array $options
   *   (optional) An array of options to control the result.
   *
   * @return \Drupal\ior_awards\Entity\AwardInterface[]
   *   An array of award entities.
   */
  public function loadByContest($contest_id, array $options = []);

  /**
   * Load all awards that have been awarded to the submission with the given ID.
   *
   * @param int|string $submission_id
   *   A submission ID.
   * @param array $options
   *   (optional) An array of options to control the result.
   *
   * @return \Drupal\ior_awards\Entity\AwardInterface[]
   *   An array of IOR award entities.
   */
  public function loadBySubmission($submission_id, array $options = []);

}
