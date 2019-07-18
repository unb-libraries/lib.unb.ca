<?php

namespace Drupal\node_path_taxonomy_group_perms\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Path taxonomy group association edit forms.
 *
 * @ingroup node_path_taxonomy_group_perms
 */
class PathTaxonomyGroupAssociationForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\node_path_taxonomy_group_perms\Entity\PathTaxonomyGroupAssociation */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;
    $simple_groups = \Drupal::entityQuery('simple_group')->execute();

    if (empty($simple_groups)) {
      $this->messenger()->addError(
        $this->t('No simple groups found to associate with paths. Please add one!')
      );
      $form['#disabled'] = TRUE;
      return $form;
    }

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
        drupal_set_message($this->t('Created the %label Path taxonomy group association.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Path taxonomy group association.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.path_taxonomy_group_association.canonical', ['path_taxonomy_group_association' => $entity->id()]);
  }

}
