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
    preg_match('/\<img ng-src="([^"]+)"/', $data, $matches);
    if ($matches !== FALSE) {
      $cover_uri = 'http:' . str_replace('_70', '_140', $matches[1]);
      return ['uri' => $cover_uri];
    }
    return FALSE;
  }

}
