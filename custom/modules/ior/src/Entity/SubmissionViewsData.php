<?php

namespace Drupal\ior\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides views integration for "submission" entities.
 */
class SubmissionViewsData extends EntityViewsData {

  /**
   * {@inheritDoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    $data['ior_submission']['moderation_state_permission'] = [
      'title' => $this->t('Moderation state permission'),
      'filter' => [
        'title' => $this->t('Moderation state permission'),
        'help' => $this->t('Filter based on permission to view submission in a given moderation state.'),
        'field' => 'moderation_state',
        'id' => 'moderation_state_permission_filter',
      ],
    ];
    return $data;
  }

}
