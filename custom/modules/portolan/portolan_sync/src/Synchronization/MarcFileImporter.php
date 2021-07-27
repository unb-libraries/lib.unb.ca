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
   * The taxonomy term mapper.
   *
   * @var \Drupal\portolan_sync\Synchronization\TaxonomyTermMapperInterface
   */
  protected $taxonomyTermMapper;

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
   * Get the taxonomy term mapper.
   *
   * @return \Drupal\portolan_sync\Synchronization\TaxonomyTermMapperInterface
   *   A taxonomy term mapper.
   */
  protected function taxonomyTermMapper() {
    return $this->taxonomyTermMapper;
  }

  /**
   * Construct a new MarcFileImporter instance.
   *
   * @param \Drupal\portolan_sync\Synchronization\ParserInterface $parser
   *   A parser to extract Portolan relevant info.
   * @param \Drupal\portolan_sync\Synchronization\TaxonomyTermMapperInterface $taxonomy_term_mapper
   *   A taxonomy term mapper.
   */
  public function __construct(ParserInterface $parser, TaxonomyTermMapperInterface $taxonomy_term_mapper) {
    $this->parser = $parser;
    $this->taxonomyTermMapper = $taxonomy_term_mapper;
  }

  /**
   * {@inheritDoc}
   */
  public function import($source, int $max_records = self::UNLIMITED) {
    $portolan_records = [];

    $index = 0;
    while (($max_records <= 0 || $index < $max_records) && $marc_record = $source->next()) {
      if (!empty($portolan_record = $this->parser()->parse($marc_record))) {
        $oclc_id = $portolan_record[PortolanRecordInterface::FIELD_OCLC_ID];
        $this->mapTaxonomyTerms($portolan_record);

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

  /**
   * Map author, descriptor, jurisdiction, location values to taxonomy terms.
   *
   * @param array $portolan_record
   *   Array of parsed portolan values.
   */
  private function mapTaxonomyTerms(array &$portolan_record) {
    if (array_key_exists(PortolanRecordInterface::FIELD_AUTHOR, $portolan_record)) {
      $portolan_record[PortolanRecordInterface::FIELD_AUTHOR] = $this
        ->taxonomyTermMapper()
        ->getTerms(PortolanRecordInterface::AUTHOR_VID, $portolan_record[PortolanRecordInterface::FIELD_AUTHOR]);
    }

    if (array_key_exists(PortolanRecordInterface::FIELD_DESCRIPTOR, $portolan_record)) {
      $portolan_record[PortolanRecordInterface::FIELD_DESCRIPTOR] = $this
        ->taxonomyTermMapper()
        ->getTerms(PortolanRecordInterface::DESCRIPTOR_VID, $portolan_record[PortolanRecordInterface::FIELD_DESCRIPTOR]);
    }

    if (array_key_exists(PortolanRecordInterface::FIELD_JURISDICTION, $portolan_record)) {
      $portolan_record[PortolanRecordInterface::FIELD_JURISDICTION] = $this
        ->taxonomyTermMapper()
        ->getTerms(PortolanRecordInterface::JURISDICTION_VID, $portolan_record[PortolanRecordInterface::FIELD_JURISDICTION]);
    }

    if (array_key_exists(PortolanRecordInterface::FIELD_LOCATION, $portolan_record)) {
      $portolan_record[PortolanRecordInterface::FIELD_LOCATION] = $this
        ->taxonomyTermMapper()
        ->getTerms(PortolanRecordInterface::LOCATION_VID, $portolan_record[PortolanRecordInterface::FIELD_LOCATION]);
    }
  }

}
