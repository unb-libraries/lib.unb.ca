<?php

namespace Drupal\node_path_taxonomy\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Node taxonomy path edit forms.
 *
 * @ingroup node_path_taxonomy
 */
class NodeTaxonomyPathForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\node_path_taxonomy\Entity\NodeTaxonomyPath */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Node taxonomy path.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Node taxonomy path.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.node_taxonomy_path.canonical', ['node_taxonomy_path' => $entity->id()]);
  }

}
