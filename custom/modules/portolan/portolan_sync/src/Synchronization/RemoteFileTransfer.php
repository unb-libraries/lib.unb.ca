<?php

namespace Drupal\portolan_sync\Synchronization;

/**
 * Transfers files between local and remote hosts.
 *
 * @package Drupal\portolan_sync\Synchronization
 */
class RemoteFileTransfer implements FileTransferInterface {

  /**
   * {@inheritDoc}
   */
  public function copy(string $from, string $to) {
    $output = $result = NULL;
    exec("scp -q -o StrictHostKeyChecking=no -i /app/keys/oclc-sftp.key {$from} {$to}", $output, $result);
    if (file_exists($to) && filesize($to) > 0) {
      return $to;
    }
    return FALSE;
  }

}
