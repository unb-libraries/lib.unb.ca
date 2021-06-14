<?php

namespace Drupal\portolan_sync\Synchronization;

use Drupal\Core\FileTransfer\FileTransfer;

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
    $to = rtrim(realpath($to), DIRECTORY_SEPARATOR);
    if (is_dir($to)) {
      $filename = pathinfo($from)['filename'];
      $to .= DIRECTORY_SEPARATOR . $filename;
    }

    file_put_contents($to, file_get_contents($from));
    return $to;
  }

}
