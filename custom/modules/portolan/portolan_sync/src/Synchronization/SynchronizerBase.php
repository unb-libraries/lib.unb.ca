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
   * @param \Drupal\Core\Entity\ContentEntityStorageInterface $storage
   *   The entity storage.
   * @param \Drupal\Core\Logger\LoggerChannelInterface|null $logger
   *   (optional) A logger channel.
   */
  public function __construct(DataImporterInterface $importer, ContentEntityStorageInterface $storage, LoggerChannelInterface $logger = NULL) {
    $this->importer = $importer;
    $this->storage = $storage;
    $this->logger = $logger;
  }

  /**
   * Import data to synchronize with the storage.
   *
   * @return array
   *   An array of records.
   */
  protected function import() {
    return $this->importer()
      ->import();
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
