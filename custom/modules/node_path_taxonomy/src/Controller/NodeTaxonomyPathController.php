<?php

namespace Drupal\node_path_taxonomy\Controller;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node_path_taxonomy\Entity\NodeTaxonomyPath;

/**
 * NodeTaxonomyPathController object.
 */
class NodeTaxonomyPathController {

  /**
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public static function setPath(&$form, FormStateInterface $form_state) {
    $path_tid = $form_state->getValue('publication_path');
    \Drupal::state()->set(NodeTaxonomyPath::getStateKey(), $path_tid);
  }

}
