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
    $output = $result = NULL;
    exec("cp {$from} {$to}", $output, $result);
    return $to;
  }

}
