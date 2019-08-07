<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Allows to place dynamic hours inside a sidebar.
 *
 * @Block(
 *   id = "upcoming_hours_block",
 *   admin_label = @Translation("Hours (Upcoming)"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class UpcomingHours extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    global $base_url;
    $config = $this->getConfiguration();

    $calendar_id = isset($config['calendar_id']) ? $config['calendar_id'] : 'libsys';
    $days = isset($config['days']) ? $config['days'] : 7;
    $hours_page_url = isset($config['hours_page_url']) ? $config['hours_page_url'] : $base_url . '/about/hours';

    $container = [
      '#type' => 'container',
      'hours_table' => [
        '#type' => 'table',
      ],
      'hours_page_url' => [
        '#type' => 'link',
        '#title' => $this->t('Complete Hours'),
        '#url' => Url::fromUri($hours_page_url),
      ],
      '#attached' => [
        'library' => [
          'calendar_hours_client/calendar-hours',
        ],
      ],
    ];

    foreach (range(0, $days - 1) as $number) {
      $date_format = $number === 0 ? '[Current]' : 'dddd';
      $container['hours_table'][] = [
        '#type' => 'html_tag',
        '#tag' => 'tr',
        '#attributes' => [
          'data-ch-id' => $calendar_id,
          'data-ch-format-date' => $date_format,
          'data-ch-format-time' => 'h:mm a',
          'data-ch-days' => $number,
        ],
      ];
    }

    return $container;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    global $base_url;

    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $form['calendar_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Calendar'),
      '#description' => $this->t('ID of the calendar to display.'),
      '#default_value' => isset($config['calendar_id']) ? $config['calendar_id'] : 'libsys',
    ];

    $form['days'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of Days'),
      '#min' => 1,
      '#step' => 1,
      '#default_value' => isset($config['days']) ? $config['days'] : 7,
      '#size' => 3,
    ];

    $form['hours_page_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Hours Page URL'),
      '#default_value' => isset($config['hours_page_url']) ? $config['hours_page_url'] : $base_url . '/about/hours',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['calendar_id'] = $values['calendar_id'];
    $this->configuration['days'] = $values['days'];
    $this->configuration['hours_page_url'] = $values['hours_page_url'];
  }

}
