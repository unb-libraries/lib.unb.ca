<?php

namespace Drupal\eresources\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\search_api\Entity\Index;
use Drupal\eresources\LocalResult;

/**
 * KB Form base class.
 */
class LocalFormBase extends FormBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'eres_' . $this->getKbFormId();
  }

  /**
   * Returns a key-value list of search options.
   */
  public function getSearchOptions() {
    return [
      'title' => 'Word(s) in title',
      'browse' => 'Title starts with',
      'exact' => 'Exact title',
      'keyword' => 'Keyword search (title, author, publisher...)',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form_state->setMethod('GET');
    $form_wrapper = $this->getKbFormId() . "_wrapper";

    $form[$form_wrapper]['#cache'] = [
      'max-age' => 0,
    ];

    $form[$form_wrapper]['#after_build'] = ['::afterBuild'];

    $form[$form_wrapper]['info'] = [
      '#markup' => '<p>' . $this->getSearchDescription() . '</p>',
    ];

    $form[$form_wrapper]['type'] = [
      '#title' => $this->t('Search Type'),
      '#title_display' => 'invisible',
      '#type' => 'radios',
      '#required' => TRUE,
      '#options' => $this->getSearchOptions(),
      '#default_value' => 'title',
    ];

    $form[$form_wrapper]['query_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'form-row',
          'flex-sm-nowrap',
        ],
      ],
    ];

    $form[$form_wrapper]['query_wrapper']['query'] = [
      '#title' => $this->t('Search'),
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => $this->getSearchPlaceholder(),
      ],
      '#id' => $this->getKbFormId() . '-query',
      '#value' => '',
    ];

    $form[$form_wrapper]['query_wrapper']['actions'] = [
      '#type' => 'actions',
    ];

    $form[$form_wrapper]['query_wrapper']['actions']['submit_button'] = [
      '#type' => 'submit',
      '#value' => $this->t('GO'),
      '#name' => '',
    ];

    $req = $this->getRequest()->query;
    $query = $req->get('query');
    if (!empty($query) && $this->getFormId() == $req->get('form_id')) {
      // The wrapper for search results.
      $form[$form_wrapper]['search_results'] = [
        // Set the results to be below the form.
        '#weight' => 100,
        '#prefix' => '<div class="search-results-wrapper mt-4 mx-n4">',
        '#suffix' => '</div>',
        // The #markup element forces rendering of the #prefix and #suffix.
        // Without content, the wrappers are not rendered. Therefore, an empty
        // string is declared, ensuring that the wrapper for the search results
        // is present when the page is loaded.
        '#markup' => '',
      ];

      $perPage = 50;
      $pagerManager = \Drupal::service('pager.manager');
      $pagerParameters = \Drupal::service('pager.parameters');
      $page = $pagerParameters->findPage();
      $start = $perPage * $page + 1;

      $form[$form_wrapper]['query_wrapper']['query']['#value'] = $query;
      $form[$form_wrapper]['type']['#default_value'] = $req->get('type');

      // $form['results_header'] = ['#markup' => '<h2 class="mt-3">Results</h2>'];
      $index = Index::load('eresources');
      $indexQuery = $index->query();

      $parseModeManager = \Drupal::service('plugin.manager.search_api.parse_mode');
      $directMode = $parseModeManager->createInstance('direct');
      $termMode = $parseModeManager->createInstance('terms');

      if (preg_match('/id:(\d+)/', $query, $matches)) {
        $indexQuery->setParseMode($directMode);
        $indexQuery->addCondition('id', $matches[1]);
      }
      else {
        switch ($req->get('type')) {
          case 'title':
            $indexQuery->setParseMode($termMode);
            $indexQuery->setFulltextFields(['title']);
            $indexQuery->keys($query);
            break;

          case 'exact':
            $indexQuery->setParseMode($directMode);
            $indexQuery->addCondition('title_browse', $query);
            break;

          case 'browse':
            $indexQuery->setParseMode($directMode);
            $fields = $index->getServerInstance()->getBackend()->getSolrFieldNames($index);
            $indexQuery->setFulltextFields(['title']);
            $indexQuery->keys("{$fields['title_browse']}:{$query}*");
            break;

          case 'keyword':
            $indexQuery->setParseMode($termMode);
            $indexQuery->keys($query);
            break;
        }
      }

      $indexQuery->addCondition('kb_data_type', $this->getKbContentType());
      $indexQuery->addCondition('status', TRUE);
      $indexQuery->range($start - 1, $perPage);

      $order = ['title', 'ASC'];
      if (!empty($req->get('order'))) {
        $order = str_replace('relevance', 'search_api_relevance', $req->get('order'));
        if (strpos($order, ',') === FALSE) {
          $order .= ',asc';
        }
        $order = explode(',', $order);
        $order[1] = strtoupper($order[1]);
      }
      $indexQuery->sort(...$order);

      $result = $indexQuery->execute();

      $total = $result->getResultCount();
      if ($total == 0) {
        $form[$form_wrapper]['search_results']['page'] = ['#markup' => "<div class='alert alert-info rounded-0'>Your search for <b>\"{$query}\"</b> returned no results.</div>"];
      }
      else {
        $entries = array_map(function ($i) {
          return new LocalResult($i);
        }, $result->getResultItems());
        $form[$form_wrapper]['search_results']['page'] = ['#markup' => "<div class='alert alert-info rounded-0'>Showing results {$start} to " . ($start + count($entries) - 1) . " of {$total} for search <b>\"{$query}\"</b>.</div>"];
        $pagerManager->createPager($total, $perPage);
        $form[$form_wrapper]['search_results']['top-pager'] = ['#type' => 'pager'];
        $form[$form_wrapper]['search_results']['results'] = [
          '#theme' => 'eresources-local',
          '#eresources' => $entries,
          '#form_id' => $this->getKbFormId(),
          '#debug' => $this->currentUser()->hasPermission('administer eresources_record entities'),
        ];
        $form[$form_wrapper]['search_results']['bottom-pager'] = ['#type' => 'pager'];
        $form[$form_wrapper]['#attached']['library'][] = 'eresources/eresources';
      }
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
    /* Class Drupal\eresources\Form\KbFormBase contains 1 abstract method and
     * must therefore be declared abstract or implement the remaining method
     * Drupal\Core\Form\FormInterface::submitForm
     */
  }

}
