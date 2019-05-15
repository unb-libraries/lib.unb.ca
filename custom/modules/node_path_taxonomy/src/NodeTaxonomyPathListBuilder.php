<?php

namespace Drupal\node_path_taxonomy;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Node taxonomy path entities.
 *
 * @ingroup node_path_taxonomy
 */
class NodeTaxonomyPathListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Node taxonomy path ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\node_path_taxonomy\Entity\NodeTaxonomyPath */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.node_taxonomy_path.edit_form',
      ['node_taxonomy_path' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
