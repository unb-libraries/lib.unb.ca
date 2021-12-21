<?php

namespace Drupal\ior_awards\Entity\Storage;

use Drupal\Core\Entity\ContentEntityStorageInterface;

/**
 * Interface for IOR collection entity storage handlers.
 */
interface CollectionStorageInterface extends ContentEntityStorageInterface {

  /**
   * Load collections containing at least one submission to the given contest.
   *
   * @param int|string $contest_id
   *   A contest ID.
   * @param array $options
   *   (optional) An array of options to control the result.
   *
   * @return \Drupal\ior_awards\Entity\CollectionInterface
   *   An array of collection entities.
   */
  public function loadByContest($contest_id, array $options = []);

}
