<?php

namespace Drupal\portolan_sync\Synchronization;

/**
 * Interface for data importers.
 *
 * @package Drupal\portolan_sync
 */
interface DataImporterInterface {

  const UNLIMITED = -1;

  /**
   * Retrieve records from a source.
   *
   * @param int $max_records
   *   The maximum number of records to import.
   *
   * @return array
   *   An array of records.
   */
  public function import(int $max_records = self::UNLIMITED);

}
