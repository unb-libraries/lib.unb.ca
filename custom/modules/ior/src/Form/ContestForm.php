<?php

namespace Drupal\ior\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Default form for IOR contest entities.
 */
class ContestForm extends ContentEntityForm {

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    if (!empty($this->getEntity()->getSubmissions())) {
      $form['field_title']['widget'][0]['#disabled'] = TRUE;
    }

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $return = parent::save($form, $form_state);

    $contest = $this->getEntity();
    $form_state->setRedirectUrl($contest->toUrl());
    $this->messenger()->addStatus($this->t('@action contest @contest.', [
      '@action' => $return === SAVED_NEW ? 'Created' : 'Updated',
      '@contest' => $contest->label(),
    ]));

    return $return;
  }

}
