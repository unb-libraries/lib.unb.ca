<?php

namespace Drupal\ior_awards\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides views integration for "IOR Collection" entities.
 */
class CollectionViewsData extends EntityViewsData {

  /**
   * {@inheritDoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    $data['ior_collection']['ior_submission'] = [
      'title' => t('Submission'),
      'help' => t('Computed counterpart of "collection" field on Submission entities.'),
      'field' => [
        'id' => 'field',
        'default_formatter' => 'entity_reference_label',
        'field_name' => 'ior_submission',
      ],
    ];
    return $data;
  }

}
