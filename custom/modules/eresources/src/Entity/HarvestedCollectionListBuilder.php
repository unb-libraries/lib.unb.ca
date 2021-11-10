<?php

namespace Drupal\eresources\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\lib_unb_custom_entity\Entity\EntityListBuilder;

/**
 * Defines a class to build a listing of harvested collection entities.
 */
class HarvestedCollectionListBuilder extends EntityListBuilder {

  /**
   * {@inheritDoc}
   */
  public function buildHeader() {
    return [
      'id' => $this->t('ID'),
      'oclc_id' => $this->t('OCLC ID'),
      'name' => $this->t('Name'),
      'default_tab' => $this->t('Default Tab'),
      'records' => $this->t('Records'),
    ] + parent::buildHeader();
  }

  /**
   * {@inheritDoc}
   */
  public function buildRow(EntityInterface $entity) {
    return [
      'id' => $entity->id(),
      'oclc_id' => $entity->get('oclc_id')->value,
      'name' => $entity->toLink(),
      'default_tab' => HarvestedCollection::$tabs[$entity->get('default_tab')->value],
      'records' => $entity->getRecordCount(),
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

}
