<?php

namespace Drupal\guides\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\key\KeyRepositoryInterface;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Metric;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Display Google Analytics stats data.
 */
class StatsFormBase extends FormBase {
  /**
   * Object.
   *
   * @var object
   */
  private $object;

  /**
   * Drupal key repository.
   *
   * @var \Drupal\key\KeyRepositoryInterface
   */
  private $keyRepository;

  /**
   * Constructor.
   */
  public function __construct(KeyRepositoryInterface $keyRepository) {
    $this->keyRepository = $keyRepository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('key.repository')
    );
  }

  /**
   * Set object.
   */
  public function setObject($object) {
    $this->object = $object;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return $this->getFormType() . '_stats_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $pagePath = $this->object->toUrl()->toString();

    $header = '<h2>Simplified Google Analytics Page Views</h2>
<div class="alert alert-info" role="alert">
  <p><i class="fa fa-info-circle" aria-hidden="true"></i> This page provides basic <a href="https://www.google.ca/search?q=google+analytics+page+view">page view stats</a> tracked through Google Analytics for guides.</p>
  <p>Stats are available for guides and category pages for which you are an editor, as well as your profile page.</p>';

    if ($this->getFormType() == 'guide') {
      $header .= '<p>Guide stats will show the tab name where possible, i.e. deleted tabs still have stats but no longer have names. Each tab is considered a separate page with respect to views. The default start tab for the guide will appear first but it may also appear again using its tab ID. This occurs when patrons navigate back to the home tab after visiting others tabs.</p>';
    }
    $header .= '<p><i class="fa fa-exclamation-triangle"></i> Google has migrated from <i>Universal Analytics</i> to <i>Goggle Analytics 4</i>. The two formats are not compatible. If you are looking for stats prior to July 2023, please submit a <a href="https://intranet.lib.unb.ca/requests/trouble-tickets">Trouble Ticket</a>.</p></div>';

    $form['header'] = [
      '#markup' => $header,
    ];

    $form['stats'] = $this->getStatsTableFor($pagePath);

    $start = '2023-06-01';
    $today = date('Y-m-d');

    $form['custom_header'] = [
      '#markup' => '<h2>Custom Date Range</h2>',
    ];

    $form['start_date'] = [
      '#type' => 'date',
      '#attributes' => ['type' => 'date', 'min' => $start, 'max' => $today],
      '#default_value' => $start,
      '#required' => TRUE,
      '#title' => $this->t('Start Date'),
    ];

    $form['end_date'] = [
      '#type' => 'date',
      '#attributes' => ['type' => 'date', 'min' => $start, 'max' => $today],
      '#default_value' => $today,
      '#required' => TRUE,
      '#title' => $this->t('End Date'),
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Generate CSV File'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $dateRanges = [
      new DateRange([
        'start_date' => $form_state->getValue('start_date'),
        'end_date' => $form_state->getValue('end_date'),
      ]),
    ];
    $dimensions = [
      new Dimension(['name' => 'pagePath']),
      new Dimension(['name' => 'yearMonth']),
    ];
    $pagePath = $this->object->toUrl()->toString();

    $data = $this->getStatsFor($pagePath, $dateRanges, $dimensions);
    $rows = $this->mergeStatsRows($data);

    if ($this->getFormType() == 'guide') {
      $rows = $this->addTabNames($rows);
    }

    $output = '';
    foreach ($rows as $row) {
      $output .= implode(',', $row) . "\n";
    }

    $response = new Response();
    $response->setContent($output);
    $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
    $response->headers->set('Content-Disposition', 'attachment; filename=stats.csv');

    $form_state->setResponse($response);
  }

  /**
   * Generate week, month and 6 months stats table.
   */
  private function getStatsTableFor($pagePath) {
    $dateRanges = [
      new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today']),
      new DateRange(['start_date' => '30daysAgo', 'end_date' => 'today']),
      new DateRange(['start_date' => '180daysAgo', 'end_date' => 'today']),
    ];

    $dimensions = [
      new Dimension(['name' => 'pagePath']),
    ];

    $data = $this->getStatsFor($pagePath, $dateRanges, $dimensions);
    $stats = [];
    foreach ($data as $result) {
      $path = $result['pagePath'];
      if (empty($stats[$path])) {
        $stats[$path] = [0, 0, 0];
      }
      $col = (int) str_replace('date_range_', '', $result['dateRange']);
      $stats[$path][$col] = $result['screenPageViews'];
    }

    ksort($stats);
    if ($this->getFormType() == 'guide') {
      $stats = $this->addTabNames($stats);
    }

    $rows = [];
    foreach ($stats as $page => $row) {
      $rows[] = array_merge([$page], $row);
    }

    return [
      '#type' => 'table',
      '#header' => [
        'Page', 'Last Week', 'Last Month', 'Last 6 Months',
      ],
      '#rows' => $rows,
      '#empty' => $this->t('No results to display.'),
    ];
  }

  /**
   * Add tab names to guide page paths.
   */
  private function addTabNames($rows) {
    $guide = $this->object;

    $tabs = [];
    foreach ($guide->sections as $section) {
      $entity = $section->entity;
      $tabs[$entity->id()] = $entity->field_section_label->value;
    }

    $newRows = [];
    foreach ($rows as $page => $data) {
      if (preg_match('/#section-(\d+)/', $page, $matches)) {
        if (!empty($tabs[$matches[1]])) {
          $page .= ' (' . $tabs[$matches[1]] . ')';
        }
      }
      $newRows[$page] = $data;
    }

    return $newRows;
  }

  /**
   * Call Google API to get pageview stats for a path.
   */
  private function getStatsFor($pagePath, $dateRanges, $dimensions) {
    $key = $this->keyRepository->getKey('google_api');
    $client = new BetaAnalyticsDataClient([
      'credentials' => $key->getKeyProvider()->getConfiguration()['file_location'],
    ]);

    $response = $client->runReport([
      'property' => 'properties/386623876',
      'dateRanges' => $dateRanges,
      'dimensions' => $dimensions,
      'dimensionFilter' => new FilterExpression([
        'filter' => new Filter([
          'field_name' => 'pagePath',
          'string_filter' => new StringFilter([
            'match_type' => MatchType::BEGINS_WITH,
            'value' => $pagePath,
            'case_sensitive' => FALSE,
          ]),
        ]),
      ]),
      'metrics' => [new Metric(['name' => 'screenPageViews'])],
    ]);

    $dHeaders = $response->getDimensionHeaders();
    $mHeaders = $response->getMetricHeaders();
    $dimensionHeaders = array_map(fn($v): string => $v->getName(), iterator_to_array($dHeaders));
    $metricHeaders = array_map(fn($v): string => $v->getName(), iterator_to_array($mHeaders));

    $results = [];
    foreach ($response->getRows() as $rowIndex => $row) {
      $data = [];
      $dimensions = $row->getDimensionValues();
      $metrics = $row->getMetricValues();

      foreach ($dimensions as $i => $dimension) {
        $entry = $dimensionHeaders[$i];
        $data[$entry] = $dimension->getValue();
      }

      foreach ($metrics as $i => $metric) {
        $entry = $metricHeaders[$i];
        $data[$entry] = $metric->getValue();
      }

      $results[] = $data;
    }

    // Tighter pagePath filtering.
    $results = array_filter($results, function ($v) use ($pagePath) {
      return $v['pagePath'] == $pagePath || preg_match("/^\Q${pagePath}\E[#/]/", $v['pagePath']);
    });

    return $results;
  }

  /**
   * Map rows of results to pageviews per month.
   */
  private function mergeStatsRows($results) {
    $data = [];
    $dates = [];
    foreach ($results as $result) {
      $data[$result['pagePath']][$result['yearMonth']] = $result['screenPageViews'];
      $date = \DateTime::createFromFormat('Ym', $result['yearMonth']);
      $dates[$result['yearMonth']] = $date->format('M Y');
    }

    ksort($dates);
    ksort($data);

    $rows = [array_merge(['Page'], array_values($dates))];
    foreach ($data as $page => $dateData) {
      $row = [$page];
      foreach (array_keys($dates) as $date) {
        $row[] = empty($dateData[$date]) ? 0 : $dateData[$date];
      }
      $rows[] = $row;
    }

    return $rows;
  }

}
