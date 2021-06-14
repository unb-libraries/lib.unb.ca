<?php

namespace Drupal\portolan_sync\Synchronization;

/**
 * Interface for source data providers.
 *
 * @package Drupal\portolan_sync\Synchronization
 */
interface DataProviderInterface {

  /**
   * Retrieve the data.
   *
   * @return mixed
   *   An object.
   */
  public function getData();

}
