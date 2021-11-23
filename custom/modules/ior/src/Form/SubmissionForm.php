<?php

namespace Drupal\ior\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Default form for "submission" entities.
 */
class SubmissionForm extends ContentEntityForm {

  /**
   * Assign contest, if not already assigned.
   */
  protected function prepareEntity() {
    parent::prepareEntity();
    if (!$this->entity->getContest()) {
      $contest = $this
        ->getRouteMatch()
        ->getParameter('contest');
      $this->entity->setContest($contest);
    }
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    if ($this->getEntity()->isNew()) {
      $form['agree_contest_rules'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('I certify that I am the copyright owner of the submitted image and have the necessary rights, permissions, and/or licenses to submit the image to the competition according to the full contest rules and conditions.'),
        '#required' => TRUE,
        '#default_value' => FALSE,
        '#weight' => 97,
      ];
    }

    return $form;
  }

}
