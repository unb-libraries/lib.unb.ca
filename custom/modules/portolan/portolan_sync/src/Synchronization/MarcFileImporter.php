<?php

namespace Drupal\portolan_sync\Synchronization;

use Drupal\portolan\Entity\PortolanRecordInterface;

/**
 * Imports Portolan metadata from OCLC.
 *
 * @package Drupal\portolan_sync
 */
class MarcFileImporter implements DataImporterInterface {

  /**
   * {@inheritDoc}
   */
  public function import($max_count = self::UNLIMITED) {
    $marc_path = $this->downloadMarcFile(__DIR__ . '/../../portolan.mrc', '/tmp');
    $portolan_records = [];

    $index = 0;
    $marc_records = new \File_MARC($marc_path);
    while (($max_count <= 0 || $index < $max_count) && $marc_record = $marc_records->next()) {
      if (!empty($portolan_record = $this->parseRecord($marc_record))) {
        $oclc_id = $portolan_record[PortolanRecordInterface::FIELD_OCLC_ID];
        $portolan_records[$oclc_id] = $portolan_record;
        // @todo Download cover image
        $index++;
      }
    }

    return $portolan_records;
  }

  /**
   * Parses the given MARC record.
   *
   * @param \File_MARC_Record $marc_record
   *   A MARC record.
   *
   * @return array
   *   An array of key => value.
   */
  protected function parseRecord(\File_MARC_Record $marc_record) {
    return [
      PortolanRecordInterface::FIELD_OCLC_ID => $this->parseOclcId($marc_record),
    ];
  }

  /**
   * Extract the OCLC ID from the given MARC record.
   *
   * @param \File_MARC_Record $marc_record
   *   The MARC record.
   *
   * @return string
   *   An OCLC ID.
   */
  protected function parseOclcId(\File_MARC_Record $marc_record) {
    $oclc_id = '';
    $fields = $marc_record->getFields('001');
    foreach ($fields as $num) {
      $num = $num->getData();
      $oclc_id = preg_replace('/[^0-9]/', '', $num);
    }
    return $oclc_id;
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
