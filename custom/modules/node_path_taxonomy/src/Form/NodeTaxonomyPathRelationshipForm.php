<?php

namespace Drupal\node_path_taxonomy\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node_path_taxonomy\Entity\NodeTaxonomyPathRelationship;
use Drupal\taxonomy\Entity\Vocabulary;

/**
 * Class NodeTaxonomyPathRelationshipForm.
 */
class NodeTaxonomyPathRelationshipForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $node_taxonomy_path_relationship = $this->entity;

    // Type.
    $type_options = NodeTaxonomyPathRelationship::getUnConfiguredNodeTypes();
    if (empty($type_options) && $form_state->getFormObject()->getOperation() == 'add') {
      $form['no_types'] = [
        '#markup' => '<p>All node types have assigned taxonomy path relationships. To change an existing relationship, first delete the existing relationship.</p>',
      ];
      $form['#disabled'] = TRUE;
      return $form;
    }

    $form['node_type'] = [
      '#type' => 'select',
      '#title' => t('Node Type'),
      '#options' => $type_options,
      '#default_value' => empty($node_taxonomy_path_relationship->getNodeType()) ? NULL : $node_taxonomy_path_relationship->getNodeType(),
      '#required' => TRUE,
    ];

    $vocabulary_options = [];
    $vocabularies = Vocabulary::loadMultiple();
    foreach ($vocabularies as $vocabulary) {
      $vocabulary_options[$vocabulary->id()] = $vocabulary->label();
    }

    $form['vid'] = [
      '#type' => 'select',
      '#options' => $vocabulary_options,
      '#title' => $this->t('Vocabulary'),
      '#default_value' => empty($node_taxonomy_path_relationship->getVid()) ? NULL : $node_taxonomy_path_relationship->getVid(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'hidden',
      '#default_value' => empty($node_taxonomy_path_relationship->id()) ? NULL : $node_taxonomy_path_relationship->id(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $node_taxonomy_path_relationship = $this->entity;
    if (!empty($node_taxonomy_path_relationship)) {
      // Existing item.
    }
    else {
      // New item.
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $node_taxonomy_path_relationship = $this->entity;

    // Provide a ID based on node type.
    $id = $form_state->getValue('id');
    $node_type = $form_state->getValue('node_type');
    if (empty($id)) {
      $form_state->setValue('id', NODE_PATH_TAXONOMY_RELATIONSHIP_ID_PREFIX . $node_type);
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $node_taxonomy_path_relationship = $this->entity;
    $status = $node_taxonomy_path_relationship->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Node taxonomy path relationship.', [
          '%label' => $node_taxonomy_path_relationship->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Node taxonomy path relationship.', [
          '%label' => $node_taxonomy_path_relationship->label(),
        ]));
    }
    $form_state->setRedirectUrl($node_taxonomy_path_relationship->toUrl('collection'));
  }

}
