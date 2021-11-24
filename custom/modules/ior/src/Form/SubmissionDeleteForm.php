<?php

namespace Drupal\ior\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Core\Url;

/**
 * Delete form for "ior_submission" entities.
 */
class SubmissionDeleteForm extends ContentEntityDeleteForm {

  /**
   * {@inheritDoc}
   */
  protected function getRedirectUrl() {
    $contest = $this->getEntity()->getContest();
    return Url::fromRoute('view.ior_submissions.page_1', [
      'contest' => $contest->id(),
    ]);
  }

}
