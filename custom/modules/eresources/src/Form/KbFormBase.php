<?php

namespace Drupal\eresources\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\eresources\KbResult;
use Drupal\oclc_api\Oclc\OclcAuthorizationInterface;
use Drupal\oclc_api\Plugin\oclc\OclcApiManagerInterface;
use Drupal\oclc_api\Plugin\oclc\OclcPluginManagerTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * KB Form base class.
 */
class KbFormBase extends FormBase {

  use StringTranslationTrait;
  use OclcPluginManagerTrait;

  /**
   * An OCLC authorizer.
   *
   * @var \Drupal\oclc_api\Oclc\OclcAuthorizationInterface
   */
  protected $oclcAuthorization;

  /**
   * Class constructor.
   */
  public function __construct(OclcApiManagerInterface $oclc_api_manager, OclcAuthorizationInterface $oclc_authorizer) {
    $this->oclcApiManager = $oclc_api_manager;
    $this->oclcAuthorization = $oclc_authorizer;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    $oclc_api_manager = $container->get('plugin.manager.oclc_api');
    $oclc_authorizer = $container->get('oclc_authorizer.worldcat_knowledge_base');
    return new static($oclc_api_manager, $oclc_authorizer);
  }

  /**
   * Retrieve the OCLC authorizer.
   *
   * @return \Drupal\oclc_api\Oclc\OclcAuthorizationInterface
   *   An oclc authorizer.
   */
  protected function oclcAuthorization() {
    return $this->oclcAuthorization;
  }

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
      $stopWords = ['and', '&'];
      $cleanQuery = preg_replace('/\b(' . implode('|', $stopWords) . ')\b/i', '', $query);
      $cleanQuery = trim(preg_replace('/\s+/', ' ', $cleanQuery));

      $search = [
        'title' => $cleanQuery,
        'orderBy' => 'title asc',
      ];
      switch ($req->get('type')) {
        case 'keyword':
          $search = ['q' => $cleanQuery];
          break;

        case 'exact':
          $search = ['title' => "\"{$query}\""];
          break;

        case 'browse':
          $search = ['title' => "{$query}%"];
          break;
      }
      if (!empty($req->get('order'))) {
        $order = str_replace(',', ' ', $req->get('order'));
        $order = str_replace('relevance', 'relevancy', $order);
        $search['orderBy'] = $order;
      }

      try {
        $api = $this->oclcApi('worldcat_knowledge_base', ['authorization' => $this->oclcAuthorization()]);
        $result = $api->get('search-entries', $search + [
          'content' => $this->getKbContentType(),
          'itemsPerPage' => $perPage,
          'startIndex' => $start,
        ]);
      }
      catch (\Exception $error) {
        \Drupal::logger('eresources')->error($error);
        $form[$form_wrapper]['search_results']['page'] = ['#markup' => '<div class="alert alert-danger rounded-0">Unable to complete your search at this time. Please try again later.</div>'];
        return $form;
      }
      $total = $result->{'os:totalResults'};
      if ($total == 0) {
        $form[$form_wrapper]['search_results']['page'] = ['#markup' => "<div class='alert alert-info rounded-0'>Your search for <b>\"{$query}\"</b> returned no results.</div>"];
      }
      else {
        $entries = array_map(function ($i) {
          return new KbResult($i);
        }, $result->entries);
        $form[$form_wrapper]['search_results']['page'] = ['#markup' => "<div class='alert alert-info rounded-0'>Showing results {$start} to " . ($start + count($entries) - 1) . " of {$total} for search <b>\"{$query}\"</b>.</div>"];
        $pagerManager->createPager($total, $perPage);
        $form[$form_wrapper]['search_results']['top-pager'] = ['#type' => 'pager'];
        $form[$form_wrapper]['search_results']['results'] = [
          '#theme' => 'eresources-kb',
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
