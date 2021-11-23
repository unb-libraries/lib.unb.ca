<?php

namespace Drupal\ior\Entity\Storage;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Cache\MemoryCache\MemoryCacheInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\custom_entity_events\Entity\EntityEventDispatcherTrait;

/**
 * Storage handler for submission entities.
 */
class SubmissionStorage extends SqlContentEntityStorage {

  use EntityEventDispatcherTrait;

  /**
   * The contest entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $contestStorage;

  /**
   * Get the contest entity storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   A storage handler for contest entities.
   */
  protected function contestStorage() {
    return $this->contestStorage;
  }

  /**
   * Create a new SubmissionStorage instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection to be used.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend to be used.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Cache\MemoryCache\MemoryCacheInterface $memory_cache
   *   The memory cache backend to be used.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeInterface $entity_type, Connection $database, EntityFieldManagerInterface $entity_field_manager, CacheBackendInterface $cache, LanguageManagerInterface $language_manager, MemoryCacheInterface $memory_cache, EntityTypeBundleInfoInterface $entity_type_bundle_info, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($entity_type, $database, $entity_field_manager, $cache, $language_manager, $memory_cache, $entity_type_bundle_info, $entity_type_manager);
    $this->contestStorage = $entity_type_manager->getStorage('contest');
  }

  /**
   * {@inheritDoc}
   */
  protected function getFromStorage(array $ids = NULL) {
    $entities = parent::getFromStorage($ids);
    foreach ($entities as $entity) {
      $contest = $this->contestStorage()->loadBySubmission($entity->id());
      $entity->setContest($contest);
    }
    return $entities;
  }

}
