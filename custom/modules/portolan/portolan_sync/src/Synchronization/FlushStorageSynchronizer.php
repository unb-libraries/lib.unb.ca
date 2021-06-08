<?php

namespace Drupal\portolan_sync\Synchronization;

/**
 * Synchronizes by flushing storage, then repopulating with imported data.
 *
 * @package Drupal\Synchronization
 */
class FlushStorageSynchronizer extends SynchronizerBase {

  protected const DELETE_CHUNK_SIZE = 500;

  /**
   * {@inheritDoc}
   */
  public function sync() {
    $imported_records = 0;
    $skipped = 0;

    $this->clearStorage();
    $records = $this->import();
    foreach ($records as $record) {
      if ($this->persist($record)) {
        $imported_records++;
      }
      else {
        $skipped++;
      }
    }
    return [
      'synced' => $imported_records,
      'skipped' => $skipped,
    ];
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

}
