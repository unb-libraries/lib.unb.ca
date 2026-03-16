<?php

namespace Drupal\lib_unb_ca_finding\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\source\Url;
use Drupal\lib_unb_ca_finding\Url\UrlEncoder;

/**
 * Source plugin that ensures URL path segments are percent-encoded.
 *
 * @MigrateSource(
 *   id = "finding_url"
 * )
 */
class FindingUrl extends Url {

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $orig = $row->getSourceProperty('url');
    if (!empty($orig) && is_string($orig)) {
      // Use your UrlEncoder helper to produce a safe URL for HTTP requests.
      $encoded = UrlEncoder::encodeUrl($orig);
      if ($encoded !== $orig) {
        $row->setSourceProperty('url', $encoded);
      }
    }

    // Preserve normal Url source behaviour.
    return parent::prepareRow($row);
  }

}