<?php

namespace Drupal\guides\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for custom local eresources.
 */
class EresourcesLocalRecordForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'add_local_eresource';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => 'Title',
      '#required' => TRUE,
      '#maxlength' => 256,
    ];

    $form['description'] = [
      '#type' => 'text_format',
      '#title' => 'Brief Description',
      '#required' => TRUE,
      '#format' => 'library_page_html',
    ];

    $form['type'] = [
      '#type' => 'select',
      '#title' => 'Resource Type',
      '#options' => [
        'REF,PHYS' => $this->t('Reference Source'),
        'DATA,PHYS' => $this->t('Article Database'),
      ],
      '#description' => $this->t('Resources are available in all tabs.'),
    ];

    $form['online'] = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => 'For online resources',
    ];

    $form['ebook'] = [
      '#type' => 'textfield',
      '#title' => 'e-Book Link or URL',
      '#description' => $this->t('Will hyperlink the title to the value in this field. Omit the proxy prefix.'),
      '#maxlength' => 256,
    ];

    $form['license'] = [
      '#type' => 'checkbox',
      '#title' => 'This is a licensed resource',
      '#description' => $this->t('The proxy prefix will be added automatically.'),
    ];

    $form['print'] = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => 'For print resources',
    ];

    $form['cat_location'] = [
      '#type' => 'textfield',
      '#title' => 'Catalogue Location',
      '#description' => $this->t('eg. HIL-REF. If you want this represented as multiple locations, enter "multiple locations" and enter the search parameter in the OCLC Number field.'),
      '#maxlength' => 256,
    ];

    $form['callnumber'] = [
      '#type' => 'textfield',
      '#title' => 'Call Number',
      '#description' => $this->t('eg. GN316 .R37 2000'),
      '#maxlength' => 256,
    ];

    $form['cat_link'] = [
      '#type' => 'number',
      '#title' => 'OCLC Number',
      '#description' => $this->t('eg. 69734537. Enter ONLY one with no spaces or hyphens. This is an OCLC Number search into WorldCat.'),
      '#maxlength' => 256,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];

    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
      '#weight' => 5,
      '#submit' => ['::cancelForm'],
      '#limit_validation_errors' => [],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('cat_location') == 'multiple locations') {
      return;
    }

    $fields = ['cat_location', 'callnumber', 'cat_link'];
    $filled = [];
    foreach ($fields as $f) {
      if (!empty($form_state->getValue($f))) {
        $filled[] = $f;
      }
    }
    if (count($filled) != 0 && count($filled) != 3) {
      foreach (array_diff($fields, $filled) as $m) {
        $form_state->setErrorByName($m, $this->t('All of "Location", "Call Number" and "OCLC Number" must be entered or none at all.'));
      }
    }
  }

  /**
   * Cancel the add/edit and return to course link list.
   */
  public function cancelForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('guides.eresources_list');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('guides.eresources_list');
    $this->messenger()->addStatus('Record created');
  }

}
