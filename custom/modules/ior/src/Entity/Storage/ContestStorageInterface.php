<?php

namespace Drupal\ior\Entity\Storage;

use Drupal\Core\Entity\ContentEntityStorageInterface;

/**
 * Interface for contest entity storage handlers.
 */
interface ContestStorageInterface extends ContentEntityStorageInterface {

  /**
   * Load the contest for the given submission ID.
   *
   * @param int $submission_id
   *   An ID of a submission entity.
   *
   * @return \Drupal\ior\Entity\ContestInterface|null
   *   A submission entity. NULL if none could be loaded.
   */
  public function loadBySubmission($submission_id);

}
