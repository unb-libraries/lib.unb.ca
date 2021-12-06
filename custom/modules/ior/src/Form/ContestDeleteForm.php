<?php

namespace Drupal\ior\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Core\Url;

/**
 * Delete from for contest entities.
 */
class ContestDeleteForm extends ContentEntityDeleteForm {

  /**
   * {@inheritDoc}
   */
  public function getDescription() {
    $submissions = $this->getEntity()->getSubmissions();
    if (!empty($submissions)) {
      return $this->t('@submission_count will be deleted. This action cannot be undone.', [
        '@submission_count' => $this->formatPlural(count($submissions), '1 submission', '@count submissions'),
      ]);
    }
    else {
      return parent::getDescription();
    }
  }

  /**
   * {@inheritDoc}
   */
  protected function getRedirectUrl() {
    return Url::fromRoute('view.ior_contests.page_1', [
      'contest' => $this->getEntity()->id(),
    ]);
  }

}
