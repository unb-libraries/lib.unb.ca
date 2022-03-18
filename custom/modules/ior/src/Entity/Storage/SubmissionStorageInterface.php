<?php

namespace Drupal\ior\Entity\Storage;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageInterface;

/**
 * Interface for storage handlers for "ior_submission" entities.
 */
interface SubmissionStorageInterface extends ContentEntityStorageInterface, RevisionableEntityStorageInterface {

  /**
   * Load submission associated with the given contest ID.
   *
   * @param int|string $contest_id
   *   The contest ID.
   * @param array $options
   *   (optional) An array of options to control the result.
   *
   * @return \Drupal\ior\Entity\SubmissionInterface[]
   *   An array of ior_submission entities.
   */
  public function loadByContest($contest_id, array $options = []);

}
