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

}
