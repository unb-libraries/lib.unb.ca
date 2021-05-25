<?php

namespace Drupal\portolan_sync\Synchronization;

/**
 * Interface for data importers.
 *
 * @package Drupal\portolan_sync
 */
interface DataImporterInterface {

  /**
   * Retrieve records from a source.
   *
   * @return array
   *   An array of records.
   */
  public function import();

}
