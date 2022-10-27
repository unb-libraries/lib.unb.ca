<?php

namespace Drupal\node_path_taxonomy\Controller;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node_path_taxonomy\Entity\NodeTaxonomyPath;
use Drupal\node_path_taxonomy\Entity\NodeTaxonomyPathRelationship;

/**
 * NodeTaxonomyPathController object.
 */
class NodeTaxonomyPathController {

  /**
   * Set the tid key for the path for later consumption.
   *
   * @param string[] $form
   *   The Drupal associative form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public static function setPath(array &$form, FormStateInterface $form_state) {
    $path_tid = $form_state->getValue('publication_path');
    \Drupal::state()->set(NodeTaxonomyPath::getPathTaxonomyTidStateKey(), $path_tid);
  }

  /**
   * Save the path tid from the form.
   *
   * @param string[] $form
   *   The Drupal associative form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public static function savePath(array &$form, FormStateInterface $form_state) {
    $node = $form_state->getFormObject()->getEntity();
    $path_tid = $form_state->getValue('publication_path');

    NodeTaxonomyPathRelationship::setNodePathRelationship($node, $path_tid);
  }

}
