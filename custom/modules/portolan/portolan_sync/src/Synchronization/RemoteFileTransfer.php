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
    $from = realpath($from);
    $to = rtrim($to, DIRECTORY_SEPARATOR);
    if (is_dir($to)) {
      $exploded_source = explode(DIRECTORY_SEPARATOR, $from);
      $file_name = array_pop($exploded_source);
      $to .= DIRECTORY_SEPARATOR . $file_name;
    }

    $output = $result = NULL;
    exec("scp -q {$from} {$to}", $output, $result);
    if ($result === 0) {
      return $to;
    }
    return FALSE;
  }

}
