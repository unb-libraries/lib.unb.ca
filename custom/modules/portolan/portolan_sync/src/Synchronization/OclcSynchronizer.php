<?php

namespace Drupal\portolan_sync\Synchronization;

use Drupal\Core\Entity\ContentEntityStorageInterface;

/**
 * Synchronizes the local Portolan dataset with OCLC.
 *
 * @package Drupal\Synchronization
 */
class OclcSynchronizer implements DataSynchronizerInterface {

  protected const DELETE_CHUNK_SIZE = 500;

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
   */
  public function __construct(DataImporterInterface $importer, ContentEntityStorageInterface $storage) {
    $this->importer = $importer;
    $this->storage = $storage;
  }

  /**
   * {@inheritDoc}
   */
  public function sync() {
    $this->clearStorage();
    $records = $this->importer()->import();
    foreach ($records as $record) {
      $this->createPortolanRecord($record)
        ->save();
    }
    return count($records);
  }

  /**
   * Remove all records from storage.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function clearStorage() {
    $query = $this->storage()->getQuery()
      ->pager(self::DELETE_CHUNK_SIZE);
    while ($records = $this->storage()->loadMultiple($query->execute())) {
      $this->storage()->delete($records);
    }
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
  protected function createPortolanRecord(array $data) {
    return $this->storage()
      ->create($data);
  }

}
