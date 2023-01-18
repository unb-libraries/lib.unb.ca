<?php

namespace Drupal\ior_awards\Entity;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\views\EntityViewsData;

/**
 * Provides views integration for "IOR Collection" entities.
 */
class CollectionViewsData extends EntityViewsData {

  use StringTranslationTrait;

  /**
   * {@inheritDoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    $data['ior_collection']['ior_submission'] = [
      'title' => $this->t('Submission'),
      'help' => $this->t('Computed counterpart of "collection" field on Submission entities.'),
      'field' => [
        'id' => 'field',
        'default_formatter' => 'entity_reference_label',
        'field_name' => 'ior_submission',
      ],
    ];
    return $data;
  }

}
