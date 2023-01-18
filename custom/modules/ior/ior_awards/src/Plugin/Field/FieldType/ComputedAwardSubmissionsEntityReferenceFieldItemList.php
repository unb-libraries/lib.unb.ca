<?php

namespace Drupal\ior_awards\Plugin\Field\FieldType;

/**
 * Reverse many-to-many IOR Award <-> IOR Submission entity reference.
 */
class ComputedAwardSubmissionsEntityReferenceFieldItemList extends ComputedEntityReferenceFieldItemList {

  /**
   * {@inheritDoc}
   */
  protected function getTargetFieldId() {
    return 'awards';
  }

}
