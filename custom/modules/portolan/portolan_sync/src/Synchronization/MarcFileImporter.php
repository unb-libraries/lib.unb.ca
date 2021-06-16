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
   * The importer for cover images.
   *
   * @var \Drupal\portolan_sync\Synchronization\DataImporterInterface
   */
  protected $coverImageImporter;

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
   * Get the cover image importer.
   *
   * @return \Drupal\portolan_sync\Synchronization\DataImporterInterface
   *   An importer object.
   */
  protected function coverImageImporter() {
    return $this->coverImageImporter;
  }

  /**
   * Construct a new MarcFileImporter instance.
   *
   * @param \Drupal\portolan_sync\Synchronization\ParserInterface $parser
   *   A parser to extract Portolan relevant info.
   * @param \Drupal\portolan_sync\Synchronization\DataImporterInterface $cover_image_importer
   *   An importer for cover images.
   */
  public function __construct(ParserInterface $parser, DataImporterInterface $cover_image_importer) {
    $this->parser = $parser;
    $this->coverImageImporter = $cover_image_importer;
  }

  /**
   * {@inheritDoc}
   */
  public function import($source, $max_records = self::UNLIMITED) {
    $portolan_records = [];

    $index = 0;
    while (($max_records <= 0 || $index < $max_records) && $marc_record = $source->next()) {
      if (!empty($portolan_record = $this->parser()->parse($marc_record))) {
        $oclc_id = $portolan_record[PortolanRecordInterface::FIELD_OCLC_ID];
        if (!array_key_exists($oclc_id, $portolan_records)) {
          $portolan_records[$oclc_id] = $portolan_record;
          $portolan_records[$oclc_id][PortolanRecordInterface::FIELD_COVER_URI] = $this
            ->coverImageImporter()
            ->import($oclc_id);
          $index++;
        }
        else {
          $portolan_records[$oclc_id] += $portolan_record;
          // @todo Batch may be incomplete (when limiting import size, holding info, e.g. call number, could be missing)
        }
        // @todo Download cover image
      }
    }

    return $portolan_records;
  }

}
