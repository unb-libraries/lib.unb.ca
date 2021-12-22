<?php

namespace Drupal\ior_awards\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Default form for IOR award entities.
 */
class AwardForm extends ContentEntityForm {

  /**
   * {@inheritDoc}
   */
  protected function prepareEntity() {
    parent::prepareEntity();
    if (!$this->entity->getSubmission()) {
      $submission = $this
        ->getRouteMatch()
        ->getParameter('ior_submission');
      $this->entity->setSubmission($submission);
    }
  }

}
