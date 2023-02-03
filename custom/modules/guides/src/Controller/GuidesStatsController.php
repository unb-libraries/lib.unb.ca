<?php

namespace Drupal\guides\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Entity\EntityInterface;
use Google\Service\AnalyticsReporting;

/**
 * Provides route responses for guides stats.
 */
class GuidesStatsController extends ControllerBase {

  /**
   * Google Analytics Reporting API client.
   *
   * @var \Google\Service\AnalyticsReporting
   */
  private $gapiAnalyticsReportingClient;

  /**
   * Constructor.
   */
  public function __construct(AnalyticsReporting $gapiAnalyticsReportingClient) {
    $this->gapiAnalyticsReportingClient = $gapiAnalyticsReportingClient;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('gapi.analyticsreporting')
    );
  }

  /**
   * Guide Stats from Google Analytics.
   */
  public function guideStats(EntityInterface $guide) {
    $render = $this->getStatsTableFor($guide);

    // Add tab names.
    $tabs = [];
    foreach ($guide->sections as $section) {
      $entity = $section->entity;
      $tabs[$entity->id()] = $entity->field_section_label->value;
    }

    $path = $guide->toUrl()->toString();
    $match = '/^' . preg_quote($path, '/') . '(?:#section-(\d+))?$/';
    for ($i = 0; $i < count($render['#stats']['#rows']); $i++) {
      if (preg_match($match, $render['#stats']['#rows'][$i][0], $matches)) {
        if (!empty($matches[1]) && !empty($tabs[$matches[1]])) {
          $render['#stats']['#rows'][$i][0] .= ' (' . $tabs[$matches[1]] . ')';
        }
      }
    }

    $render['#object_type'] = 'guide';
    $render['#title'] = 'Statistics for ' . $guide->label();

    return $render;
  }

  /**
   * Category Stats from Google Analytics.
   */
  public function categoryStats(EntityInterface $guide_category) {
    $render = $this->getStatsTableFor($guide_category);

    $render['#object_type'] = 'category';
    $render['#title'] = 'Statistics for ' . $guide_category->label();

    return $render;
  }

  /**
   * User Stats from Google Analytics.
   */
  public function userStats(UserInterface $user) {
    $render = $this->getStatsTableFor($user);

    $render['#object_type'] = 'user';
    $render['#title'] = 'Statistics for ' . $user->field_first_name->getString() . ' ' . $user->field_last_name->getString();

    return $render;
  }

  /**
   * Generate week, month and 6 months stats table.
   */
  private function getStatsTableFor($object) {
    $path = $object->toUrl()->toString();

    $ranges = [
      ['7daysAgo', 'today'],
      ['30daysAgo', 'today'],
      ['180daysAgo', 'today'],
    ];

    $data = [];
    for ($dr = 0; $dr < count($ranges); $dr++) {
      $results = $this->getStatsFor($path, $ranges[$dr], ['ga:pagePath']);
      foreach ($results as $result) {
        if (empty($data[$result['ga:pagePath']])) {
          $data[$result['ga:pagePath']] = array_fill(0, count($ranges), 0);
        }
        $data[$result['ga:pagePath']][$dr] = $result['pageviews'];
      }
    }

    $rows = [];
    ksort($data);
    foreach ($data as $page => $row) {
      $rows[] = array_merge([$page], $row);
    }

    return [
      '#theme' => 'guides_stats',
      '#stats' => [
        '#type' => 'table',
        '#header' => [
          'Page', 'Last Week', 'Last Month', 'Last 6 Months',
        ],
        '#rows' => $rows,
        '#empty' => $this->t('No results to display.'),
      ],
      '#form' => \Drupal::formBuilder()->getForm('Drupal\guides\Form\StatsForm'),
    ];
  }

  /**
   * Call Google API to get pageview stats for a path.
   */
  public function getStatsFor($path, $dates, $reportDimensions) {
    $analytics = $this->gapiAnalyticsReportingClient;

    $view_id = $this->config('guides.settings')->get('analytics_view_id');

    $dateRange = new \Google_Service_AnalyticsReporting_DateRange();
    $dateRange->setStartDate($dates[0]);
    $dateRange->setEndDate($dates[1]);

    // Create the Metrics object.
    $views = new \Google_Service_AnalyticsReporting_Metric();
    $views->setExpression("ga:pageviews");
    $views->setAlias("pageviews");

    $dimensions = [];
    foreach ($reportDimensions as $reportDimension) {
      $dimension = new \Google_Service_AnalyticsReporting_Dimension();
      $dimension->setName($reportDimension);
      $dimensions[] = $dimension;
    }

    // Create the Filter object.
    $filter = new \Google_Service_AnalyticsReporting_DimensionFilter();
    $filter->setDimensionName('ga:pagePath');
    $filter->setOperator('BEGINS_WITH');
    $filter->setExpressions([$path]);
    $filterClause = new \Google_Service_AnalyticsReporting_DimensionFilterClause();
    $filterClause->setFilters([$filter]);

    // Create the ReportRequest object.
    $request = new \Google_Service_AnalyticsReporting_ReportRequest();
    $request->setViewId($view_id);
    $request->setMetrics([$views]);
    $request->setDimensions($dimensions);
    $request->setDimensionFilterClauses([$filterClause]);
    $request->setDateRanges([$dateRange]);

    $body = new \Google_Service_AnalyticsReporting_GetReportsRequest();
    $body->setReportRequests([$request]);

    $results = [];

    $reports = $analytics->reports->batchGet($body);
    for ($reportIndex = 0; $reportIndex < count($reports); $reportIndex++) {
      $report = $reports[$reportIndex];
      $header = $report->getColumnHeader();
      $dimensionHeaders = $header->getDimensions();
      $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
      $rows = $report->getData()->getRows() ?? [];

      for ($rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
        $data = [];
        $row = $rows[$rowIndex];
        $dimensions = $row->getDimensions();
        $metrics = $row->getMetrics();

        for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
          $data[$dimensionHeaders[$i]] = $dimensions[$i];
        }

        for ($j = 0; $j < count($metrics); $j++) {
          $values = $metrics[$j]->getValues();
          for ($k = 0; $k < count($values); $k++) {
            $entry = $metricHeaders[$k];
            $data[$entry->getName()] = $values[$k];
          }
        }

        $results[] = $data;
      }
    }

    // Filter results to match specific path.
    $stats = [];
    $re = '/^' . preg_quote($path, '/') . '[^0-9a-zA-Z]/';
    foreach ($results as $result) {
      if ($result['ga:pagePath'] == $path || preg_match($re, $result['ga:pagePath'])) {
        $stats[] = $result;
      }
    }

    return $stats;
  }

}
