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
    'ebooks' => [
      'kb_data_type' => 'ebooks',
      'title' => 'e-Book',
      'form_id' => 'eres_ebooks',
    ],
    'journals' => [
      'kb_data_type' => 'jour',
      'title' => 'Journal',
      'form_id' => 'eres_journals',
    ],
    'newspapers' => [
      'kb_data_type' => 'news',
      'title' => 'Newspaper',
      'form_id' => 'eres_journals',
    ],
    'videos' => [
      'kb_data_type' => 'video',
      'title' => 'Video',
      'form_id' => 'eres_videos',
    ],
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
    return self::$typeLookup[$type]['title'] . ' Collections';
  }

  /**
   * Displays collection list by type.
   *
   * @return array
   *   A simple renderable array.
   */
  public function view($type) {
    if (!isset(self::$typeLookup[$type])) {
      throw new NotFoundHttpException();
    }

    $typeInfo = self::$typeLookup[$type];

    $searchLink = "/e-resources/?form_id={$typeInfo['form_id']}";
    $typeLower = strtolower($typeInfo['title']);
    $render = [
      'header' => [
        '#markup' => "Browse {$typeLower} collections. <a href=\"{$searchLink}\">Search or browse for <b>individual</b> {$typeLower} titles.</a>",
      ],
    ];

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
    $indexQuery->addCondition('kb_data_type', $typeInfo['kb_data_type']);
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
        '#form_id' => $typeInfo['form_id'],
      ];
      $render['bottom-pager'] = ['#type' => 'pager'];
    }

    $render['#attached']['library'][] = 'eresources/eresources';
    return $render;
  }

}
