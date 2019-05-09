<?php

namespace Drupal\node_path_taxonomy\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Node taxonomy path entities.
 */
class NodeTaxonomyPathViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
