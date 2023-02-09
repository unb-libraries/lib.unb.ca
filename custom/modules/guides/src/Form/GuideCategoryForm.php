<?php

namespace Drupal\guides\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Default form for guide_category entities.
 */
class GuideCategoryForm extends ContentEntityForm {

  /**
   * {@inheritDoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $return = parent::save($form, $form_state);

    $entity = $this->getEntity();
    $form_state->setRedirectUrl($entity->toUrl());
    $this->messenger()->addStatus($this->t('@action guide category "@guide_category."', [
      '@action' => $return === SAVED_NEW ? 'Created' : 'Updated',
      '@guide_category' => $entity->label(),
    ]));

    return $return;
  }

}
