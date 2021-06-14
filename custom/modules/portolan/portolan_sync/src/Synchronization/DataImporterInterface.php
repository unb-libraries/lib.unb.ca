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
   * @param mixed $source
   *   The source holding the data to import.
   * @param int $max_records
   *   The maximum number of records to import.
   *
   * @return array
   *   An array of records.
   */
  public function import($source, int $max_records = self::UNLIMITED);

}
