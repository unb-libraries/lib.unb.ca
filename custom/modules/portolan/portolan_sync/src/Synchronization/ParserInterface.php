<?php

namespace Drupal\portolan_sync\Synchronization;

/**
 * Interface for parsers.
 *
 * @package Drupal\portolan_sync\Synchronization
 */
interface ParserInterface {

  /**
   * Parse the given data.
   *
   * @param mixed $data
   *   The data.
   *
   * @return array
   *   An array of extracted KEY => VALUE pairs.
   */
  public function parse($data);

}
