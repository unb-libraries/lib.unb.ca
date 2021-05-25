<?php

namespace Drupal\portolan_sync\Synchronization;

/**
 * Synchronizes the local Portolan dataset with an external source.
 *
 * @package Drupal\Synchronization
 */
interface DataSynchronizerInterface {

  /**
   * Synchronize the local with the external dataset.
   */
  public function sync();

}
