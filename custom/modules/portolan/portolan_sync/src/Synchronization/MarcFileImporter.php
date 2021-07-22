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
  public function import($source, $max_records = self::UNLIMITED) {
    $portolan_records = [];

    $index = 0;
    while (($max_records <= 0 || $index < $max_records) && $marc_record = $source->next()) {
      if (!empty($portolan_record = $this->parser()->parse($marc_record))) {
        $oclc_id = $portolan_record[PortolanRecordInterface::FIELD_OCLC_ID];
        if (!array_key_exists($oclc_id, $portolan_records)) {
          $portolan_records[$oclc_id] = $portolan_record;
          $index++;
        }
        else {
          $portolan_records[$oclc_id] += $portolan_record;
          // @todo Batch may be incomplete (when limiting import size, holding info, e.g. call number, could be missing)
        }
      }
    }

    return $portolan_records;
  }

}
