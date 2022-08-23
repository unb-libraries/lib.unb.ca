<?php

namespace Drupal\guides\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\editor\Ajax\EditorDialogSave;
use Drupal\Core\Ajax\CloseModalDialogCommand;

/**
 * Eresources selector widget for ckeditor.
 */
class EresourcesDialog extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'guides_eresources_dialog';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['eresources'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'eresource_record',
      '#title' => $this->t('Resources'),
      '#required' => TRUE,
    ];

    $form['keyresources'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of resources to designate as "Key Resources"'),
    ];

    $form['searchbox'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable WorldCat targeted database search for these resources'),
    ];

    $form['noheadings'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable headings'),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['save_modal'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#submit' => [],
      '#ajax' => [
        'callback' => '::submitForm',
        'event' => 'click',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $response->addCommand(new EditorDialogSave($form_state->getValues()));
    $response->addCommand(new CloseModalDialogCommand());

    return $response;
  }

}
