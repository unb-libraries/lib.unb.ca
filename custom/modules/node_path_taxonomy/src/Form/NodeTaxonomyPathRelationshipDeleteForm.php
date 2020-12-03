<?php

namespace Drupal\node_path_taxonomy\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Builds the form to delete Node taxonomy path relationship entities.
 */
class NodeTaxonomyPathRelationshipDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $markup_block = [];
    $markup_block['title'] = [
      '#markup' => '<h2>Lorem ipsum dolor sit amet</h2>',
    ];
    $markup_block['description'] = [
      '#markup' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ac tincidunt risus. Pellentesque tempor consequat cursus. Aenean venenatis tellus vel tellus faucibus, ut varius est ornare. Sed eget cursus nisi. Duis et nibh metus. Donec feugiat gravida erat, nec tincidunt magna. Curabitur vulputate non ligula ac maximus. Phasellus pretium varius risus eu egestas.</p><p>in nunc luctus, lobortis velit eu, placerat quam. Fusce ornare mauris mi, vel dignissim augue tincidunt ut. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In a posuere orci. Vestibulum maximus orci et neque pellentesque, et ullamcorper sem molestie. Duis vitae leo erat. Nunc at dignissim enim. Aenean turpis lacus, tincidunt a laoreet eu, commodo vel diam. Mauris ut dictum ex, at tincidunt nisi. Nullam pulvinar ornare auctor. Donec id scelerisque lectus, vel posuere metus.</p>',
    ];
    array_unshift($form, $markup_block);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete %name?', ['%name' => $this->entity->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->entity->delete();

    $this->messenger()->addMessage(
      $this->t('content @type: deleted @label.',
        [
          '@type' => $this->entity->bundle(),
          '@label' => $this->entity->label(),
        ]
        )
    );

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.node_taxonomy_path_relationship.collection');
  }

}
