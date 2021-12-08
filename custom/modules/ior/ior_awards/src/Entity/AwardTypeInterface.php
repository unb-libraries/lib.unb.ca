<?php

namespace Drupal\ior_awards\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Interface for award type entities.
 */
interface AwardTypeInterface extends ConfigEntityInterface {

  const QUANTITY_FLEXIBLE = 0;

  /**
   * Get the number of times this type can be awarded, per contest.
   *
   * @return int
   *   An integer >= 0. 0 indicates a flexible number of awards.
   */
  public function getQuantity();

  /**
   * Get the importance this type holds in a contest.
   *
   * @return int
   *   A positive integer. A lower number indicates a higher importance.
   */
  public function getWeight();

}
