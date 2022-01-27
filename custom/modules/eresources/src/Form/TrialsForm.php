<?php

namespace Drupal\eresources\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * KB Journals form.
 */
class TrialsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'eres_trials';
  }

  /**
   * Form title for tabbed display.
   *
   * @return string
   *   Form title.
   */
  public static function getTitle() {
    return 'Trials';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['trials'] = [
      '#markup' => '<p>Trials</p>',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
