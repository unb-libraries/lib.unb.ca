<?php

namespace Drupal\eresources\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\lib_unb_custom_entity\Entity\EntityListBuilder;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Datetime\DateFormatterInterface;

/**
 * Defines a class to build a listing of harvested collection entities.
 */
class HarvestedCollectionListBuilder extends EntityListBuilder {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * IncidentReportListBuilder constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param Drupal\Core\Datetime\DateFormatterInterface $dateFormatter
   *   The date formatter service.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, DateFormatterInterface $dateFormatter) {
    parent::__construct($entity_type, $storage);
    $this->dateFormatter = $dateFormatter;
  }

  /**
   * {@inheritDoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')
        ->getStorage($entity_type->id()),
      $container->get('date.formatter'));
  }

  /**
   * {@inheritDoc}
   */
  public function buildHeader() {
    return [
      'id' => $this->t('ID'),
      'oclc_id' => $this->t('OCLC ID'),
      'name' => $this->t('Name'),
      'default_tab' => $this->t('Default Tab'),
      'last_sync' => $this->t('Last Sync'),
      'records' => $this->t('Records'),
    ] + parent::buildHeader();
  }

  /**
   * {@inheritDoc}
   */
  public function buildRow(EntityInterface $entity) {
    $last_sync = $entity->get('last_sync')->value;
    if (!empty($last_sync)) {
      $last_sync_value = new DrupalDateTime($last_sync, new \DateTimeZone('UTC'));
      $last_sync = $this->dateFormatter->format($last_sync_value->getTimestamp(), 'custom', 'Y-m-d H:i:s');
    }

    return [
      'id' => $entity->id(),
      'oclc_id' => $entity->get('oclc_id')->value,
      'name' => $entity->toLink(),
      'default_tab' => HarvestedCollection::$tabs[$entity->get('default_tab')->value],
      'last_sync' => $last_sync,
      'records' => $this->getStorage()->getRecordCount($entity),
    ] + parent::buildRow($entity);
  }

  /**
   * {@inheritDoc}
   */
  protected function sortKeys() {
    return [
      'name' => 'DESC',
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getOperations(EntityInterface $entity) {
    $operations = parent::getOperations($entity);
    $operations['synchronize-harvested-collection'] = [
      'title' => $this->t('Synchronize'),
      'weight' => 90,
      'url' => $this->ensureDestination($entity->toUrl('synchronize')),
    ];
    return $operations;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    $build = parent::render();
    $build['table']['#attributes'] = [
      'class' => ['admin-operations'],
    ];
    return $build;
  }

}
