<?php

namespace Drupal\ior_awards\Plugin\Field\FieldType;

use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;

/**
 * Compute reverse entity references for IOR submission's "awards" field.
 */
class ComputedEntityReferenceFieldItemList extends EntityReferenceFieldItemList {

  use ComputedItemListTrait;

  /**
   * {@inheritDoc}
   */
  protected function computeValue() {
    $target_type_id = $this->getSetting('target_type');
    $target_storage = \Drupal::entityTypeManager()->getStorage($target_type_id);
    $target_ids = $target_storage
      ->getQuery()
      ->condition('field_awards', $this->getEntity()->id(), 'CONTAINS')
      ->execute();
    foreach ($target_ids as $index => $target_id) {
      $this->list[] = $this->createItem($index, $target_id);
    }
  }

}
