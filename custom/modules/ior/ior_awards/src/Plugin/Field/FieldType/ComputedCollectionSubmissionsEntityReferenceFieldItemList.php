<?php

namespace Drupal\ior_awards\Plugin\Field\FieldType;

/**
 * Reverse many-to-many IOR Collection <-> IOR Submission entity reference.
 */
class ComputedCollectionSubmissionsEntityReferenceFieldItemList extends ComputedEntityReferenceFieldItemList {

  /**
   * {@inheritDoc}
   */
  protected function getTargetFieldId() {
    return 'collections';
  }

}
