<?php

namespace Drupal\portolan_sync\Synchronization;

use GuzzleHttp\ClientInterface;

/**
 * Imports cover Class WorldCatCoverImageImporter.
 *
 * @package Drupal\portolan_sync\Synchronization
 */
class WorldCatCoverImageImporter implements DataImporterInterface {

  /**
   * The URL to a single WorldCat record.
   *
   * @var string
   */
  protected $worldCatRecordUrl;

  /**
   * The HTTP service.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $http;

  /**
   * The cover image parser.
   *
   * @var \Drupal\portolan_sync\Synchronization\ParserInterface
   */
  protected $coverImageParser;

  /**
   * Get the URL to a single WorldCat record.
   *
   * @return string
   *   A string.
   */
  protected function getWorldCatRecordUrl() {
    return $this->worldCatRecordUrl;
  }

  /**
   * Get the HTTP service.
   *
   * @return \GuzzleHttp\ClientInterface
   *   An HTTP client object.
   */
  protected function http() {
    return $this->http;
  }

  /**
   * Get the cover image parser.
   *
   * @return \Drupal\portolan_sync\Synchronization\ParserInterface
   *   A parser object.
   */
  protected function coverImageParser() {
    return $this->coverImageParser;
  }

  /**
   * WorldCatCoverImageImporter constructor.
   *
   * @param string $world_cat_url
   *   The WorldCat base URL, i.e. the part before /<OCLC_ID>.
   * @param \GuzzleHttp\ClientInterface $http
   *   An HTTP client object.
   * @param \Drupal\portolan_sync\Synchronization\ParserInterface $coverImageParser
   *   A parser capable of parsing WorldCat HTML for cover image URIs.
   */
  public function __construct(string $world_cat_url, ClientInterface $http, ParserInterface $coverImageParser) {
    $this->worldCatRecordUrl = $world_cat_url;
    $this->http = $http;
    $this->coverImageParser = $coverImageParser;
  }

  /**
   * {@inheritDoc}
   */
  public function import($source, int $max_records = self::UNLIMITED) {
    if ($world_cat_html = $this->getHtml($source)) {
      return $this->coverImageParser()
        ->parse($world_cat_html);
    }
    return FALSE;
  }

  /**
   * Retrieve HTML from WorldCat.
   *
   * @param string $oclc_id
   *   An OCLC ID.
   *
   * @return string
   *   An HTML formatted string.
   */
  protected function getHtml(string $oclc_id) {
    try {
      return $this->http()
        ->get($this->getWorldCatRecordUrl() . $oclc_id)
        ->getBody()
        ->getContents();
    }
    catch (\Exception $e) {
      return FALSE;
    }

  }

}
