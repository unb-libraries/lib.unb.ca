<?php

namespace Drupal\portolan_sync\Synchronization;

/**
 * Interface for file transfer.
 *
 * @package Drupal\portolan_sync\Synchronization
 */
interface FileTransferInterface {

  /**
   * Download a file from the given location.
   *
   * @param string $from
   *   The location of the source file.
   * @param string $to
   *   The location to which to copy the file.
   *
   * @return string
   *   The path to the copied file.
   */
  public function copy(string $from, string $to);

}
