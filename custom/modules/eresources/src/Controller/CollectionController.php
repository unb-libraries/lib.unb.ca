<?php

namespace Drupal\eresources\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\oclc_api\Oclc\OclcAuthorizationInterface;
use Drupal\oclc_api\Plugin\oclc\OclcApiManagerInterface;
use Drupal\oclc_api\Plugin\oclc\OclcPluginManagerTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\eresources\KbResult;

/**
 * Provides route responses for the lib_core module.
 */
class CollectionController extends ControllerBase {

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
   * Gets the page title based on the type.
   *
   * @param string $collection_uid
   *   Collection UID.
   *
   * @return string
   *   The title.
   */
  public function title($collection_uid) {
    $api = $this->oclcApi('worldcat_knowledge_base', ['authorization' => $this->oclcAuthorization()]);

    try {
      $result = $api->get('read-collection', [
        'collection_uid' => $collection_uid,
      ]);
    }
    catch (\Exception $error) {
      return NULL;
    }

    return $result->title;
  }

  /**
   * Displays collection titles by collection UID.
   *
   * @return array
   *   A simple renderable array.
   */
  public function view($collection_uid) {
    $perPage = 50;
    $pagerManager = \Drupal::service('pager.manager');
    $pagerParameters = \Drupal::service('pager.parameters');
    $page = $pagerParameters->findPage();
    $start = $perPage * $page + 1;

    $api = $this->oclcApi('worldcat_knowledge_base', ['authorization' => $this->oclcAuthorization()]);
    $result = $api->get('search-entries', [
      'content' => 'fulltext,print',
      'collection_uid' => $collection_uid,
      'itemsPerPage' => $perPage,
      'startIndex' => $start,
    ]);

    $total = $result->{'os:totalResults'};
    if ($total == 0) {
      $render['results'] = ['#markup' => "<div class='alert alert-info rounded-0'>No results found</div>"];
    }
    else {
      $entries = array_map(function ($i) {
        return new KbResult($i);
      }, $result->entries);

      $render['page'] = ['#markup' => "<div class='alert alert-info rounded-0'>Showing results {$start} to " . ($start + count($entries) - 1) . " of {$total}.</div>"];
      $pagerManager->createPager($total, $perPage);
      $render['top-pager'] = ['#type' => 'pager'];
      $render['results'] = [
        '#prefix' => '<div id="search_results_wrapper" class="mt-4">',
        '#suffix' => '</div>',
        '#theme' => 'eresources',
        '#eresources' => $entries,
        '#form_id' => '',
      ];
      $render['bottom-pager'] = ['#type' => 'pager'];
    }

    $render['#attached']['library'][] = 'eresources/eresources';
    return $render;
  }

}