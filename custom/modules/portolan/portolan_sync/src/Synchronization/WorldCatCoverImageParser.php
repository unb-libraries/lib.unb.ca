<?php

namespace Drupal\portolan_sync\Synchronization;

/**
 * Parses WorldCat HTML for cover image URIs.
 *
 * @package Drupal\portolan_sync\Synchronization
 */
class WorldCatCoverImageParser implements ParserInterface {

  /**
   * {@inheritDoc}
   */
  public function parse($data) {
    return [
      'uri' => 'https://unb.on.worldcat.org/20210526120449/resources/images/default/coverart/book_printbook.jpg'
    ];
  }

}
