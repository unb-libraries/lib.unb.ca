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
   *
   * @return array
   *   An array containing the following keys:
   *   - synced: (int) number of synced records.
   *   - skipped: (int) number of skipped records.
   */
  public function sync();

}
