<?php

namespace Drupal\eresources\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\search_api\Entity\Index;
use Drupal\eresources\LocalResult;

/**
 * Provides route responses for the lib_core module.
 */
class CollectionsController extends ControllerBase {

  /**
   * Package type to KB type lookup.
   *
   * @var array
   */
  private static $typeLookup = [
    'videos' => 'video',
    'ebooks' => 'ebooks',
  ];

  /**
   * Gets the page title based on the type.
   *
   * @param string $type
   *   Package type.
   *
   * @return string
   *   The title.
   */
  public function title($type) {
    return rtrim(ucfirst($type), 's') . ' Collections';
  }

  /**
   * Displays package list by type.
   *
   * @return array
   *   A simple renderable array.
   */
  public function view($type) {
    if (!isset(self::$typeLookup[$type])) {
      throw new NotFoundHttpException();
    }

    $index = Index::load('eresources');
    $indexQuery = $index->query();
    $parseMode = \Drupal::service('plugin.manager.search_api.parse_mode')->createInstance('direct');
    $indexQuery->setParseMode($parseMode);

    $perPage = 50;
    $pagerManager = \Drupal::service('pager.manager');
    $pagerParameters = \Drupal::service('pager.parameters');
    $page = $pagerParameters->findPage();
    $start = $perPage * $page + 1;

    $indexQuery->addCondition('status', TRUE);
    $indexQuery->addCondition('kb_data_type', self::$typeLookup[$type]);
    $indexQuery->addCondition('metadata_local_is_collection', TRUE);
    $indexQuery->sort('title', 'ASC');
    $indexQuery->range($start - 1, $perPage);
    $result = $indexQuery->execute();

    $total = $result->getResultCount();
    if ($total == 0) {
      $render['results'] = ['#markup' => "<div class='alert alert-info rounded-0'>No results found</div>"];
    }
    else {
      $entries = array_map(function ($i) {
        return new LocalResult($i);
      }, $result->getResultItems());

      $render['page'] = ['#markup' => "<div class='alert alert-info rounded-0'>Showing results {$start} to " . ($start + count($entries) - 1) . " of {$total}.</div>"];
      $pagerManager->createPager($total, $perPage);
      $render['top-pager'] = ['#type' => 'pager'];
      $render['results'] = [
        '#prefix' => '<div id="search_results_wrapper" class="mt-4">',
        '#suffix' => '</div>',
        '#theme' => 'eresources',
        '#eresources' => $entries,
        '#form_id' => $type,
      ];
      $render['bottom-pager'] = ['#type' => 'pager'];
    }

    return $render;
  }

}
