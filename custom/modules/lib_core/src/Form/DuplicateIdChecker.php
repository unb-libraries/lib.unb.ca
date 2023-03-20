<?php

namespace Drupal\lib_core\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Search login IDs in the patron database.
 */
class DuplicateIdChecker extends FormBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'patron_manager_login_lookup_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form_state->setMethod('GET');

    $form['#cache'] = [
      'max-age' => 0,
    ];
    $form['#action'] = Url::fromRoute('lib_core.duplicate_id_checker')->toString();
    $form['#after_build'] = ['::afterBuild'];

    $form['pq'] = [
      '#title' => $this->t('Login ID'),
      '#type' => 'textfield',
      '#required' => TRUE,
    ];

    $form['submit_button'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
      '#name' => '',
    ];

    if (!empty($query = $this->getRequest()->query->get('pq'))) {
      $form['pq']['#value'] = $query;
      $form['results_header'] = ['#markup' => '<h2 class="mt-3">Results</h2>'];

      Database::setActiveConnection('patrons');
      $db = Database::getConnection();

      $search = $db->select('patrons', 'p')
        ->fields('p', ['login'])
        ->condition('login', $query);

      $result = $search->countQuery()->execute()->fetchField();

      if ($result == 1) {
        $form['patron-search-results'] = ['#markup' => '<p><span class="text-danger fas fa-times-circle"></span> Login ID in use.</p>'];
      }
      else {
        $form['patron-search-results'] = ['#markup' => '<p><span class="text-success fas fa-check-circle"></span> Login ID available.</p>'];
      }

      Database::setActiveConnection();
    }

    return $form;
  }

  /**
   * After build hook to remove elements from being submitted as GET variables.
   */
  public function afterBuild(array $element, FormStateInterface $form_state) {
    // Remove the form_token, form_build_id and form_id from the GET parameters.
    unset($element['form_token']);
    unset($element['form_build_id']);
    unset($element['form_id']);

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
