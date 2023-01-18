<?php

namespace Drupal\ior_awards\Entity;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\views\EntityViewsData;

/**
 * Provides views integration for "IOR Award" entities.
 */
class AwardViewsData extends EntityViewsData {

  use StringTranslationTrait;

  /**
   * {@inheritDoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    $data['ior_award']['ior_submission'] = [
      'title' => $this->t('Submission'),
      'help' => $this->t('Computed counterpart of "awards" field on Submission entities.'),
      'field' => [
        'id' => 'field',
        'default_formatter' => 'entity_reference_label',
        'field_name' => 'ior_submission',
      ],
    ];
    return $data;
  }

}
