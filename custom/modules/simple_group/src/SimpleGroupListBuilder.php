<?php

namespace Drupal\simple_group;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Simple group entities.
 *
 * @ingroup simple_group
 */
class SimpleGroupListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['name'] = $this->t('Group Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\simple_group\Entity\SimpleGroup */
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.simple_group.edit_form',
      ['simple_group' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
