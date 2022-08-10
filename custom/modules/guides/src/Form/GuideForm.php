<?php

namespace Drupal\guides\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Default form for guide entities.
 */
class GuideForm extends ContentEntityForm {

  /**
   * {@inheritDoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $return = parent::save($form, $form_state);

    $entity = $this->getEntity();
    $form_state->setRedirectUrl($entity->toUrl());
    $this->messenger()->addStatus($this->t('@action guide "@guide."', [
      '@action' => $return === SAVED_NEW ? 'Created' : 'Updated',
      '@guide' => $entity->label(),
    ]));

    return $return;
  }

}
