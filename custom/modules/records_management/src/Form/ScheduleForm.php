<?php

namespace Drupal\records_management\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Default form for "Retention schedule" entities.
 */
class ScheduleForm extends ContentEntityForm {

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $form['#attributes'] = [
      'class' => [
        'theme-dark',
      ],
    ];

    return $form;
  }

}
