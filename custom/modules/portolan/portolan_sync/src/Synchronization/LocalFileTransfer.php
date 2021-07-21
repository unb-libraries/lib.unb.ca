<?php

namespace Drupal\portolan_sync\Synchronization;

/**
 * Transfers local files.
 *
 * @package Drupal\portolan_sync\Synchronization
 */
class LocalFileTransfer implements FileTransferInterface {

  /**
   * {@inheritDoc}
   */
  public function copy(string $from, string $to) {
    file_put_contents($to, file_get_contents($from));
    return $to;
  }

}
