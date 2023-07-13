<?php

namespace Drupal\eresources\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\eresources\LocalResult;
use Drupal\search_api\Entity\Index;

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

    $entries = $this->getTrials();
    if (empty($entries)) {
      $form['trials'] = [
        '#markup' => '<p>There are no current trials.</p>',
      ];
    }
    else {
      $form['trials']['header'] = ['#markup' => '<h3 class="pb-2 mb-2 border-bottom">Browse Current Trials:</h3>'];
      $form['trials']['search_results'] = [
        '#weight' => 100,
        '#prefix' => '<div class="mt-4 mx-n4">',
        '#suffix' => '</div>',
        '#markup' => '',
      ];
      $form['trials']['search_results']['results'] = [
        '#theme' => 'trials',
        '#eresources' => $entries,
      ];
      $form['trials']['#attached']['library'][] = 'eresources/eresources';
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * Get array of trials.
   */
  public static function getTrials() {
    $index = Index::load('eresources');

    $indexQuery = $index->query();
    $parseMode = \Drupal::service('plugin.manager.search_api.parse_mode')->createInstance('direct');
    $parseMode->setConjunction('AND');
    $indexQuery->setParseMode($parseMode);
    $indexQuery->addCondition('metadata_local_license_status', 'T');
    $indexQuery->addCondition('metadata_local_subscription_end_date', date('Y-m-d'), '>=');
    $indexQuery->addCondition('status', TRUE);
    $indexQuery->range(0, 50);
    $indexQuery->sort('title', 'ASC');

    $result = $indexQuery->execute();
    $total = $result->getResultCount();
    if ($total !== 0) {
      $entries = array_map(function ($i) {
        return new LocalResult($i);
      }, $result->getResultItems());
      return $entries;
    }

    return [];
  }

}
