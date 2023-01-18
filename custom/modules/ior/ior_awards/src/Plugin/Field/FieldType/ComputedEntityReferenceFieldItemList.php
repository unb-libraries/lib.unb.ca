<?php

namespace Drupal\ior_awards\Plugin\Field\FieldType;

use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;

/**
 * Base class for computed, reverse many-to-many entity references.
 */
abstract class ComputedEntityReferenceFieldItemList extends EntityReferenceFieldItemList {

  use ComputedItemListTrait;

  /**
   * Get the field ID of the target entity.
   *
   * @return string
   *   An entity field ID.
   */
  abstract protected function getTargetFieldId();

  /**
   * {@inheritDoc}
   */
  protected function computeValue() {
    $target_type_id = $this->getSetting('target_type');
    $target_storage = \Drupal::entityTypeManager()->getStorage($target_type_id);
    $target_ids = $target_storage
      ->getQuery()
      ->condition($this->getTargetFieldId(), $this->getEntity()->id(), 'CONTAINS')
      ->execute();
    foreach ($target_ids as $index => $target_id) {
      $this->list[] = $this->createItem($index, $target_id);
    }
  }

}
