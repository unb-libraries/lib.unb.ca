<?php

namespace Drupal\records_management\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Builds a listing of "Retention schedule" entities.
 */
class ScheduleListBuilder extends EntityListBuilder {

  /**
   * {@inheritDoc}
   */
  public function buildHeader() {
    return [
      'label' => $this->t('Label'),
      'number' => $this->t('Number'),
    ] + parent::buildHeader();
  }

  /**
   * {@inheritDoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\records_management\Entity\ScheduleInterface $schedule */
    $schedule = $entity;

    return [
      'label' => Link::fromTextAndUrl($entity->label(), $schedule->toUrl()),
      'number' => "{$schedule->getClassification()->getCode()}{$schedule->getNumber()}",
    ] + parent::buildRow($entity);
  }

}
