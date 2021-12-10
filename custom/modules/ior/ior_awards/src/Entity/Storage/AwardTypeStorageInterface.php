<?php

namespace Drupal\ior_awards\Entity\Storage;

use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;

/**
 * Interface for award type entity storage handlers.
 */
interface AwardTypeStorageInterface extends ConfigEntityStorageInterface {

  /**
   * Load all award types that can be awarded.
   *
   * @param int|string $contest_id
   *   A contest ID.
   *
   * @return \Drupal\ior_awards\Entity\AwardTypeInterface[]
   *   An array of award types which "quantity" value is higher than the
   *   number of awards of that type already awarded to a submission to the
   *   contest with the given ID.
   */
  public function loadUnawarded($contest_id);

}
