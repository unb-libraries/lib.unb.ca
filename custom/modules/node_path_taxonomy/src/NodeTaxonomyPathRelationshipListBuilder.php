<?php

namespace Drupal\node_path_taxonomy;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Node taxonomy path relationship entities.
 */
class NodeTaxonomyPathRelationshipListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['node_type'] = $this->t('Node Type');
    $header['vid'] = $this->t('Vocabulary');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['node_type'] = $entity->getNodeType();
    $row['vid'] = $entity->getVid();
    // You probably want a few more properties here...
    return $row + parent::buildRow($entity);
  }

}
