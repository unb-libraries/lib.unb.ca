<?php

namespace Drupal\eresources\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\eresources\Entity\HarvestedCollection;

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

}
