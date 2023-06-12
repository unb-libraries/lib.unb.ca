<?php

namespace Drupal\guides\Form;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Generate CSV stats data.
 */
class StatsForm extends FormBase {

  /**
   * Class Resolver service.
   *
   * @var \Drupal\Core\DependencyInjection\ClassResolverInterface
   */
  protected $classResolver;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\DependencyInjection\ClassResolverInterface $classResolver
   *   The class resolver service.
   */
  public function __construct(ClassResolverInterface $classResolver) {
    $this->classResolver = $classResolver;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('class_resolver')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'guides_stats_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $start = '2017-09-01';
    $today = date('Y-m-d');

    $form['start_date'] = [
      '#type' => 'date',
      '#attributes' => ['type' => 'date', 'min' => $start, 'max' => $today],
      '#default_value' => $start,
      '#required' => TRUE,
      '#title' => $this
        ->t('Start Date'),
    ];

    $form['end_date'] = [
      '#type' => 'date',
      '#attributes' => ['type' => 'date', 'min' => $start, 'max' => $today],
      '#default_value' => $today,
      '#required' => TRUE,
      '#title' => $this
        ->t('End Date'),
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
    $dates = [
      $form_state->getValue('start_date'),
      $form_state->getValue('end_date'),
    ];
    $dimensions = ['ga:pagePath', 'ga:yearMonth'];
    $controller = $this->classResolver->getInstanceFromDefinition('\Drupal\guides\Controller\GuidesStatsController');
    $route = $this->getRouteMatch();

    $params = $route->getParameters()->all();
    $object = reset($params);
    $path = $object->toUrl()->toString();
    $results = $controller->getStatsFor($path, $dates, $dimensions);

    // Add tab names.
    if ($guide = $route->getParameter('guide')) {
      $tabs = [];
      foreach ($guide->sections as $section) {
        $entity = $section->entity;
        $tabs[$entity->id()] = $entity->field_section_label->value;
      }

      $match = '/^' . preg_quote($path, '/') . '(?:#section-(\d+))?$/';
      for ($i = 0; $i < count($results); $i++) {
        if (preg_match($match, $results[$i]['ga:pagePath'], $matches)) {
          if (!empty($matches[1]) && !empty($tabs[$matches[1]])) {
            $results[$i]['ga:pagePath'] .= ' (' . $tabs[$matches[1]] . ')';
          }
        }
      }
    }

    $rows = $this->mergeStatsRows($results);
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
   * Map rows of results to pageviews per month.
   */
  private function mergeStatsRows($results) {
    $data = [];
    $dates = [];
    foreach ($results as $result) {
      $data[$result['ga:pagePath']][$result['ga:yearMonth']] = $result['pageviews'];
      $dates[$result['ga:yearMonth']] = 1;
    }

    ksort($dates);
    ksort($data);

    $rows = [array_merge(['Page'], array_keys($dates))];
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
