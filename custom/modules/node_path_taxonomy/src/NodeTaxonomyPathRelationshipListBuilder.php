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

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    // dpm($build);
    $markup_block = [];
    $markup_block['title'] = [
      '#markup' => '<h2>Lorem ipsum dolor sit amet</h2>',
    ];
    $markup_block['description'] = [
      '#markup' => '<p>By associating a node type with a path alias taxonomy, you provide the user with a selectable list of base paths to use for each node\'s path alias.</p>',
    ];
    array_unshift($build, $markup_block);
    return $build;
  }

}
