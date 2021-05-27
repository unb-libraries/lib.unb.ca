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
   * The parser to extract Portolan relevant info.
   *
   * @var \Drupal\portolan_sync\Synchronization\ParserInterface
   */
  protected $parser;

  /**
   * Get the parser.
   *
   * @return \Drupal\portolan_sync\Synchronization\ParserInterface
   *   A parser instance.
   */
  protected function parser() {
    return $this->parser;
  }

  /**
   * Construct a new MarcFileImporter instance.
   *
   * @param \Drupal\portolan_sync\Synchronization\ParserInterface $parser
   *   A parser to extract Portolan relevant info.
   */
  public function __construct(ParserInterface $parser) {
    $this->parser = $parser;
  }

  /**
   * {@inheritDoc}
   */
  public function import($max_count = self::UNLIMITED) {
    $marc_path = $this->downloadMarcFile(__DIR__ . '/../../portolan.mrc', '/tmp');
    $portolan_records = [];

    $index = 0;
    $marc_records = new \File_MARC($marc_path);
    while (($max_count <= 0 || $index < $max_count) && $marc_record = $marc_records->next()) {
      if (!empty($portolan_record = $this->parser()->parse($marc_record))) {
        $oclc_id = $portolan_record[PortolanRecordInterface::FIELD_OCLC_ID];
        $portolan_records[$oclc_id] = $portolan_record;
        // @todo Download cover image
        $index++;
      }
    }

    return $portolan_records;
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
