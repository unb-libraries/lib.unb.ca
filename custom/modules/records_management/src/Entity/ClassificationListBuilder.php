<?php

namespace Drupal\records_management\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Builds a table listing of "functional classification" entities.
 */
class ClassificationListBuilder extends EntityListBuilder {

  /**
   * {@inheritDoc}
   */
  public function buildHeader() {
    return [
      'code' => $this->t('Code'),
      'name' => $this->t('Name'),
      'description' => $this->t('Description'),
    ] + parent::buildHeader();
  }

  /**
   * {@inheritDoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\records_management\Entity\ClassificationInterface $classification */
    $classification = $entity;

    return [
      'code' => $classification->getCode(),
      'name' => $classification->getName(),
      'description' => $classification->getDescrition(),
    ] + parent::buildRow($entity);
  }

}
