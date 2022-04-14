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

    $title = $result->title;
    $content = \Drupal::request()->query->get('content');
    if (!empty($content)) {
      $title .= " ({$content})";
    }

    return $title;
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

    try {
      $api = $this->oclcApi('worldcat_knowledge_base', ['authorization' => $this->oclcAuthorization()]);
      $params = [
        'collection_uid' => $collection_uid,
        'itemsPerPage' => $perPage,
        'startIndex' => $start,
      ];
      $content = \Drupal::request()->query->get('content');
      if (!empty($content)) {
        $params += ['content' => $content];
      }
      $result = $api->get('search-entries', $params);
    }
    catch (\Exception $error) {
      \Drupal::logger('eresources')->error($error);
      return [
        '#markup' => '<div class="alert alert-danger rounded-0">Unable to retrieve collections this time. Please try again later.</div>',
      ];
    }

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
        '#prefix' => '<div class="search-results-wrapper mt-4">',
        '#suffix' => '</div>',
        '#theme' => 'eresources-kb',
        '#eresources' => $entries,
        '#form_id' => '',
      ];
      $render['bottom-pager'] = ['#type' => 'pager'];
    }

    $render['#attached']['library'][] = 'eresources/eresources';
    return $render;
  }

}
