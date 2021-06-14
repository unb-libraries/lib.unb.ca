<?php

namespace Drupal\portolan_sync\Synchronization;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\lib_core\Logger\LoggerChannelTrait;
use Drupal\portolan\Entity\PortolanRecordInterface;

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
   * The data provider.
   *
   * @var \Drupal\portolan_sync\Synchronization\DataProviderInterface
   */
  protected $dataProvider;

  /**
   * The storage handler.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageInterface
   */
  protected $storage;

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
   * Retrieve the data provider.
   *
   * @return \Drupal\portolan_sync\Synchronization\DataProviderInterface
   *   A data provider object.
   */
  protected function provider() {
    return $this->dataProvider;
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
   * @param \Drupal\portolan_sync\Synchronization\DataProviderInterface $provider
   *   The data provider.
   * @param \Drupal\Core\Entity\ContentEntityStorageInterface $storage
   *   The entity storage.
   * @param \Drupal\Core\Logger\LoggerChannelInterface|null $logger
   *   (optional) A logger channel.
   */
  public function __construct(DataImporterInterface $importer, DataProviderInterface $provider, ContentEntityStorageInterface $storage, LoggerChannelInterface $logger = NULL) {
    $this->importer = $importer;
    $this->dataProvider = $provider;
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
    $source = $this->provider()->getData();
    return $this->importer()->import($source, $max_records);
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
