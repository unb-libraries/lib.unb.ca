<?php

namespace Drupal\portolan_sync\Synchronization;

/**
 * Returns a random cover image URI.
 *
 * @package Drupal\portolan_sync\Synchronization
 */
class WorldCatRandomCoverImageImporter implements DataImporterInterface {

  /**
   * {@inheritDoc}
   */
  public function import($source, int $max_records = self::UNLIMITED) {
    $cover_uris = [
      'http://coverart.oclc.org/ImageWebSvc/oclc/+-+41223436_140.jpg',
      'http://coverart.oclc.org/ImageWebSvc/oclc/+-+81456510_140.jpg',
      'http://coverart.oclc.org/ImageWebSvc/oclc/+-+22646910_140.jpg',
      'http://coverart.oclc.org/ImageWebSvc/oclc/+-+92916119_140.jpg'
    ];
    $index = rand(0, 10);
    if (array_key_exists($index, $cover_uris)) {
      return $cover_uris[$index];
    }
    return FALSE;
  }

}
