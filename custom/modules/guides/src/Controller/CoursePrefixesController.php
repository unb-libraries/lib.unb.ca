<?php

namespace Drupal\guides\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;

/**
 * Lists course prefixes and the guide each one links to from D2L.
 */
class CoursePrefixesController extends ControllerBase {

  /**
   * Fields that must be empty for a course link to be a prefix-only link.
   */
  const NON_PREFIX_FIELDS = [
    'course_number',
    'year',
    'term',
    'campus',
    'section',
    'exact_name',
  ];

  /**
   * Build the "Current Course Prefixes" page.
   */
  public function list() {
    $courseLinkStorage = $this->entityTypeManager()->getStorage('course_link');

    // Prefix-only course links on published guides: the same set the D2L
    // redirect falls back to (see CourseLinkController::findLmsMatch()).
    $query = $courseLinkStorage->getQuery()->accessCheck(FALSE)
      ->exists('prefix')
      ->condition('guide.entity.status', 1)
      ->sort('id');
    foreach (self::NON_PREFIX_FIELDS as $field) {
      $query->notExists($field);
    }
    $courseLinks = $courseLinkStorage->loadMultiple($query->execute());

    // Group guides by prefix. Sorted by ID so the first guide listed for a
    // prefix is the one the D2L redirect will choose.
    $matched = [];
    foreach ($courseLinks as $courseLink) {
      $guide = $courseLink->guide->entity;
      if (!$guide) {
        continue;
      }
      $prefix = strtoupper(trim($courseLink->prefix->value));
      $matched[$prefix][$guide->id()] = $guide;
    }

    $rows = [];
    $matchedPrefixes = array_keys($matched);
    sort($matchedPrefixes, SORT_STRING);
    foreach ($matchedPrefixes as $prefix) {
      foreach ($matched[$prefix] as $guide) {
        $url = $guide->toUrl('canonical', ['absolute' => TRUE]);
        $rows[] = [
          $prefix,
          $guide->label(),
          Link::fromTextAndUrl($url->toString(), $url),
        ];
      }
    }

    $unmatched = array_diff($this->getKnownPrefixes(), $matchedPrefixes);
    sort($unmatched, SORT_STRING);

    $build['description'] = [
      '#markup' => '<p>' . $this->t('Course prefixes with a guide that is the default D2L link for that prefix (a course link with only a prefix set, on a published guide). If more than one guide is listed for a prefix, the first is the one D2L will link to.') . '</p>',
    ];

    $build['matched'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Prefix'),
        $this->t('Guide'),
        $this->t('Link'),
      ],
      '#rows' => $rows,
      '#empty' => $this->t('No guides are linked to a course prefix.'),
    ];

    $build['unmatched_heading'] = [
      '#markup' => '<h2>' . $this->t('Prefixes without a guide') . '</h2>',
    ];

    $build['unmatched'] = [
      '#theme' => 'item_list',
      '#items' => $unmatched,
      '#attributes' => ['class' => ['course-prefixes-unmatched']],
      '#empty' => $this->t('Every known course prefix has a guide.'),
    ];

    $build['#attached']['library'][] = 'guides/course-prefixes';

    $build['#cache'] = [
      'tags' => [
        'course_link_list',
        'guide_list',
      ],
    ];

    return $build;
  }

  /**
   * Course prefixes known from the Colleague course section exports.
   *
   * @return string[]
   *   List of prefixes.
   */
  protected function getKnownPrefixes() {
    $file = $this->moduleHandler()->getModule('guides')->getPath() . '/data/course_prefixes.txt';
    if (!is_readable($file)) {
      return [];
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return array_unique(array_filter(array_map(
      fn($line) => strtoupper(trim($line)),
      $lines
    )));
  }

}
