<?php

namespace Drupal\ior\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * List builder for award type entities.
 */
class AwardTypeListBuilder extends EntityListBuilder {

  /**
   * {@inheritDoc}
   */
  public function buildHeader() {
    return [
      'name' => $this->t('Name'),
      'quantity' => $this->t('Quantity'),
    ] + parent::buildHeader();
  }

  /**
   * {@inheritDoc}
   */
  public function buildRow(EntityInterface $entity) {
    return [
      'name' => $entity->label(),
      'quantity' => $entity->getQuantity(),
    ] + parent::buildRow($entity);
  }

}
