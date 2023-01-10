<?php

namespace Drupal\ior\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Default form for submission type entities.
 */
class SubmissionTypeForm extends EntityForm {

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    /** @var \Drupal\ior\Entity\SubmissionType $submission_type */
    $submission_type = $this->getEntity();

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $submission_type->label(),
      '#description' => $this->t("Label for the Location type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#title' => $this->t('ID'),
      '#default_value' => $submission_type->id(),
      '#disabled' => $submission_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\ior\Entity\SubmissionType::load',
      ],
    ];

    return $form;
  }

}
