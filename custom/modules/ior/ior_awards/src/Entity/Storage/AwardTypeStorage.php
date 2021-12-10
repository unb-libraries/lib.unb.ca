<?php

namespace Drupal\ior_awards\Entity\Storage;

use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Cache\MemoryCache\MemoryCacheInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\ior_awards\Entity\AwardTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Storage handler for IOR award entities.
 */
class AwardTypeStorage extends ConfigEntityStorage implements AwardTypeStorageInterface {

  /**
   * The IOR award storage.
   *
   * @var \Drupal\ior_awards\Entity\Storage\AwardStorageInterface
   */
  protected $awardStorage;

  /**
   * Get the IOR award storage.
   *
   * @return \Drupal\ior_awards\Entity\Storage\AwardStorageInterface
   *   A storage handler for IOR award entities.
   */
  protected function awardStorage() {
    return $this->awardStorage;
  }

  /**
   * Create a new AwardType storage instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Component\Uuid\UuidInterface $uuid_service
   *   The UUID service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Cache\MemoryCache\MemoryCacheInterface $memory_cache
   *   The memory cache backend.
   * @param \Drupal\ior_awards\Entity\Storage\AwardStorageInterface $award_storage
   *   The IOR award storage.
   */
  public function __construct(EntityTypeInterface $entity_type, ConfigFactoryInterface $config_factory, UuidInterface $uuid_service, LanguageManagerInterface $language_manager, MemoryCacheInterface $memory_cache, AwardStorageInterface $award_storage) {
    parent::__construct($entity_type, $config_factory, $uuid_service, $language_manager, $memory_cache);
    $this->awardStorage = $award_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    /** @var \Drupal\ior_awards\Entity\Storage\AwardStorageInterface $award_storage */
    $award_storage = $container->get('entity_type.manager')->getStorage('ior_award');
    return new static(
      $entity_type,
      $container->get('config.factory'),
      $container->get('uuid'),
      $container->get('language_manager'),
      $container->get('entity.memory_cache'),
      $award_storage
    );
  }

  /**
   * {@inheritDoc}
   */
  public function loadUnawarded($contest_id) {
    /** @var \Drupal\ior_awards\Entity\AwardTypeInterface[] $types */
    $types = $this->loadMultiple();

    $available_types = [];
    $groups = $this->groupByType($this->loadAwards($contest_id));
    foreach ($types as $type_id => $type) {
      $quantity = $type->getQuantity();
      if (!array_key_exists($type_id, $groups)) {
        $available_types[$type_id] = $type;
      }
      else {
        $awards = $groups[$type_id];
        if ($quantity === AwardTypeInterface::QUANTITY_FLEXIBLE || $quantity > count($awards)) {
          $available_types[$type_id] = $type;
        }
      }
    }
    return $available_types;
  }

  /**
   * Load awarded awards for the given contest ID.
   *
   * @param int|string $contest_id
   *   A contest ID.
   *
   * @return \Drupal\ior_awards\Entity\AwardInterface[]
   *   Ann array of IOR award entities.
   */
  protected function loadAwards($contest_id) {
    return $this->awardStorage()
      ->loadByContest($contest_id);
  }

  /**
   * Group the given awards by award type.
   *
   * @param \Drupal\ior_awards\Entity\AwardInterface[] $awards
   *   An array of IOR award entities.
   *
   * @return array
   *   A nested array of IOR award entities, keyed annd grouped by award type.
   */
  protected function groupByType(array $awards) {
    $groups = [];
    foreach ($awards as $award) {
      if (!array_key_exists($type = $award->getTypeId(), $groups)) {
        $groups[$type] = [];
      }
      $groups[$type][$award->id()] = $award;
    }
    return $groups;
  }

}
