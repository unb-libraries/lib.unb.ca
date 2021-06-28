<?php

namespace Drupal\eresources\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\StringTranslation\StringTranslationTrait;
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
    return 'eresources_' . $this->getKbFormId() . '_form';
  }

  /**
   * Returns a key-value list of search options.
   */
  public function getSearchOptions() {
    return [
      'title' => 'Word(s) in title',
      'exact' => 'Exact title',
      'browse' => 'Title starts with',
      'keyword' => 'Keyword search (title, author, publisher...)',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form_state->setMethod('GET');

    $form['#cache'] = [
      'max-age' => 0,
    ];
    $form['#action'] = Url::fromRoute('eresources.' . $this->getKbFormId() . '_form')->toString();
    $form['#after_build'] = ['::afterBuild'];

    $form['info'] = [
      '#markup' => '<p>' . $this->getSearchDescription() . '</p>',
    ];

    $form['query'] = [
      '#title' => $this->t('Query'),
      '#type' => 'textfield',
      '#required' => TRUE,
    ];

    $form['type'] = [
      '#title' => $this->t('Search Type'),
      '#type' => 'radios',
      '#required' => TRUE,
      '#options' => $this->getSearchOptions(),
      '#default_value' => 'title',
    ];

    $form['submit_button'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
      '#name' => '',
    ];

    $req = $this->getRequest()->query;
    $query = $req->get('query');
    if (!empty($query)) {
      $perPage = 50;
      $page = pager_find_page();
      $start = $perPage * $page + 1;

      $form['query']['#value'] = $query;
      $form['type']['#default_value'] = $req->get('type');

      $form['results_header'] = ['#markup' => '<h2 class="mt-3">Results</h2>'];

      $api = $this->oclcApi('worldcat_knowledge_base', ['authorization' => $this->oclcAuthorization()]);
      $search = ['title' => $query];
      switch ($req->get('type')) {
        case 'keyword':
          $search = ['q' => $query];
          break;

        case 'exact':
          $search = ['title' => "\"{$query}\""];
          break;

        case 'browse':
          $search = ['title' => "{$query}%"];
          break;
      }
      $result = $api->get('search-entries', $search + [
        'content' => $this->getKbContentType(),
        'itemsPerPage' => $perPage,
        'startIndex' => $start,
      ]);
      $total = $result->{'os:totalResults'};
      if ($total == 0) {
        $form['page'] = ['#markup' => "<p>Your search for <b>\"{$query}\"</b> returned no results.</p>"];
      }
      else {
        $entries = $result->entries;
        $form['page'] = ['#markup' => "<p>Showing results {$start} to " . ($start + count($entries) - 1) . " of {$total} for search <b>\"{$query}\"</b>.</p>"];
        pager_default_initialize($total, $perPage);
        $form['top-pager'] = ['#type' => 'pager'];
        $form['results'] = [
          '#theme' => 'eresources',
          '#eresources' => $entries,
        ];
        $form['bottom-pager'] = ['#type' => 'pager'];
        $form['#attached']['library'][] = 'eresources/eresources';
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
  }

}
