<?php

namespace Drupal\spaces\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;

/**
 * Computes a space entity's "campus" value.
 */
class CampusFieldItemList extends FieldItemList {

  use ComputedItemListTrait;

  /**
   * {@inheritDoc}
   */
  protected function computeValue() {
    /** @var \Drupal\spaces\Entity\SpaceInterface $space */
    $space = $this->getEntity();

    while (!$space->hasField('field_campus')) {
      $space = $space->getParent();
    }
    $this->list[0] = $space->get('field_campus');
  }

}
