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
    $form['preamble'] = [
      '#markup' => '<div class="alert alert-info" role="alert"><i class="fas fa-info-circle"></i> Trial resources are generally <b>NOT available from off-campus</b>. UNB Libraries <b>does not license</b> these products and therefore may not have access to all aspects of the product nor do we have full technical support.</div>',
    ];

    $form['trials'] = [
      '#markup' => '<p>There are no current trials.</p>',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
