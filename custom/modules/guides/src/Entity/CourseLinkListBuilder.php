<?php

namespace Drupal\guides\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of course links for a guide.
 */
class CourseLinkListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    $build['table']['#empty'] = 'No course links have been added to this guide yet.';

    $guide = \Drupal::routeMatch()->getParameters()->get('guide');

    $build['description'] = [
      '#markup' => $this->t('<p>Entries found here are <strong>PRIMARILY</strong> used by the system to direct patrons to the most appropriate Guide for their <strong>D2L</strong> course. They also help define whether this is a subject-level guide or a specific course guide. It is not necessary to have any values entered and improper configuration can disrupt proper linking. If it doubt, contact Jeff.</p>
<ul>
<li>Specific course guides must minimally have a <strong>PREFIX</strong> and a <strong>COURSE NUMBER</strong>.</li>
<li>to have further granularity, optionally add YEAR, TERM and SECTION</li>
<li>to link multiple D2L courses to a particular guide, i.e. History 2254 and HIST 4255 or ENGL 2134 and FILM 2134, multiple PREFIX/COURSE NUMBER sets may be entered. Each linked course <strong>MUST</strong> have a separate entry</li>
<li>guides with <strong>ONLY a PREFIX</strong> entered, i.e. ANTH, are intended to be the default guide for linking all D2L courses with that course prefix</li>
</ul>'),
      '#weight' => -1,
    ];

    $build['add_course_link_button'] = [
      '#type' => 'operations',
      '#links' => [
        'add' => [
          'title' => $this->t('Add a new course link'),
          'url' => Url::fromRoute(
            'entity.course_link.add_form',
            [
              'guide' => $guide,
            ]
          ),
        ],
      ],
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['year'] = $this->t('Year');
    $header['term'] = $this->t('Term');
    $header['campus'] = $this->t('Campus');
    $header['prefix'] = $this->t('Prefix');
    $header['course_number'] = $this->t('Course Number');
    $header['section'] = $this->t('Section');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['year'] = $entity->year->value;
    $row['term'] = $entity->term->value;
    $row['campus'] = $entity->campus->value;
    $row['prefix'] = $entity->prefix->value;
    $row['coures_number'] = $entity->course_number->value;
    $row['section'] = $entity->section->value;

    return $row + parent::buildRow($entity);
  }

}
