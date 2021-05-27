<?php

namespace Drupal\portolan_sync\Synchronization;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Imports Portolan metadata from OCLC.
 *
 * @package Drupal\portolan_sync
 */
class MarcFileImporter implements DataImporterInterface {

  /**
   * {@inheritDoc}
   */
  public function import() {
    $marc_path = $this->downloadMarcFile(__DIR__ . '/../../portolan.mrc', '/tmp');
    return $marc_path;
  }

  /**
   * Download a file from the given location.
   *
   * @param string $source
   *   The location of the file to download.
   * @param string $destination
   *   The location at which to store the downloaded file.
   *
   * @return string
   *   The path to the downloaded file.
   */
  protected function downloadMarcFile(string $source, string $destination) {
    $source = realpath($source);
    $destination = rtrim($destination, DIRECTORY_SEPARATOR);

    $output = $result = NULL;
    exec("scp -q {$source} {$destination}", $output, $result);

    if ($result === 0) {
      if (is_dir($destination)) {
        $exploded_source = explode(DIRECTORY_SEPARATOR, $source);
        $file_name = array_pop($exploded_source);
        $path = $destination . DIRECTORY_SEPARATOR . $file_name;
      }
      elseif (is_file($destination)) {
        $path = $destination;
      }
    }

    return $path;
  }

}
