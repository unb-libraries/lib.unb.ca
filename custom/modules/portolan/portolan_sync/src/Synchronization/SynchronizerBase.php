<?php

namespace Drupal\portolan_sync\Synchronization;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\lib_core\Logger\LoggerChannelTrait;
use Drupal\portolan\Entity\PortolanRecordInterface;
use GuzzleHttp\ClientInterface;

/**
 * Base class for synchronizer implementations.
 *
 * @package Drupal\portolan_sync\Synchronization
 */
abstract class SynchronizerBase implements DataSynchronizerInterface {

  use LoggerChannelTrait;

  /**
   * The data importer.
   *
   * @var \Drupal\portolan_sync\DataImporterInterface
   */
  protected $importer;

  /**
   * The storage handler.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageInterface
   */
  protected $storage;

  /**
   * The parser for cover images.
   *
   * @var \Drupal\portolan_sync\Synchronization\ParserInterface
   */
  protected $coverImageParser;

  /**
   * An HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $http;

  /**
   * Get the data importer.
   *
   * @return \Drupal\portolan_sync\DataImporterInterface
   *   A data importer instance.
   */
  protected function importer() {
    return $this->importer;
  }

  /**
   * Get the cover image parser.
   *
   * @return \Drupal\portolan_sync\Synchronization\ParserInterface
   *   A parser instance.
   */
  protected function coverImageParser() {
    return $this->coverImageParser;
  }

  /**
   * Get the HTTP client.
   *
   * @return \GuzzleHttp\ClientInterface
   *   An HTTP client instance.
   */
  protected function http() {
    return $this->http;
  }

  /**
   * Get the storage handler.
   *
   * @return \Drupal\Core\Entity\ContentEntityStorageInterface
   *   A storage handler for portolan_record entities.
   */
  protected function storage() {
    return $this->storage;
  }

  /**
   * Create a DataSynchronizer instance.
   *
   * @param \Drupal\portolan_sync\Synchronization\DataImporterInterface $importer
   *   The data importer.
   * @param \Drupal\portolan_sync\Synchronization\ParserInterface $coverImageParser
   *   The cover image parser.
   * @param \GuzzleHttp\ClientInterface $http
   *   The HTTP client.
   * @param \Drupal\Core\Entity\ContentEntityStorageInterface $storage
   *   The entity storage.
   * @param \Drupal\Core\Logger\LoggerChannelInterface|null $logger
   *   (optional) A logger channel.
   */
  public function __construct(DataImporterInterface $importer, ParserInterface $coverImageParser, ClientInterface $http, ContentEntityStorageInterface $storage, LoggerChannelInterface $logger = NULL) {
    $this->importer = $importer;
    $this->coverImageParser = $coverImageParser;
    $this->http = $http;
    $this->storage = $storage;
    $this->logger = $logger;
  }

  /**
   * Import data to synchronize with the storage.
   *
   * @param int $max_records
   *   (optional) The maximum number of records to import. Defaults to importing
   *   all available records.
   *
   * @return array
   *   An array of records.
   */
  protected function import($max_records = DataImporterInterface::UNLIMITED) {
    $records = $this->importer()->import($max_records);
    foreach ($records as $oclc_id => &$record) {
      if ($cover_uri = $this->getCoverUri($oclc_id)) {
        $record[PortolanRecordInterface::FIELD_COVER_URI] = $cover_uri;
      }
    }
    return $records;
  }

  /**
   * Retrieve a cover image URI for the given OCLC ID.
   *
   * @param string $oclc_id
   *   An OCLC ID.
   *
   * @return string|false
   *   An absolute URI.
   */
  protected function getCoverUri(string $oclc_id) {
    $html = $this->http()
      ->get("https://unb.on.worldcat.org/oclc/{$oclc_id}")
      ->getBody()
      ->getContents();
    $cover_uri = $this
      ->coverImageParser()
      ->parse($html)['uri'];
    if ($cover_uri) {
      return $cover_uri;
    }
    return FALSE;
  }

  /**
   * Create a Portolan record with the given data.
   *
   * @param array $data
   *   An array of key-value pairs.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   An entity instance.
   */
  protected function persist(array $data) {
    try {
      if ($portolan_entity = $this->storage()->create($data)) {
        return $portolan_entity->save();
      }
      return FALSE;
    }
    catch (\Exception $e) {
      $this->error("Record @oclc_id could not be imported: @error", [
        '@oclc_id' => $data[PortolanRecordInterface::FIELD_OCLC_ID],
        '@error' => $e->getMessage(),
      ]);
      return FALSE;
    }
  }

}
