<?php

namespace Drupal\node_path_taxonomy;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node_path_taxonomy\Entity\NodeTaxonomyPathRelationship;
use Drupal\pathauto\PathautoWidget;

/**
 * Extends the core path widget.
 */
class NodeTaxonomyPathPathautoWidget extends PathautoWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    if (NodeTaxonomyPathRelationship::getRelationshipSetFromFormState($form_state)) {
      $user = \Drupal::currentUser();
      if (!$user->hasPermission('override node path taxonomy node paths')) {
        $element['pathauto']['#disabled'] = TRUE;
        $element['pathauto']['#description'] = t('The path for this item will be generated automatically by the Node Path Taxonomy module.');
      }
      else {
        $element['pathauto']['#description'] = t('If checked, the path for this item will be generated automatically by the Node Path Taxonomy module.');
      }
    }
    return $element;
  }

}