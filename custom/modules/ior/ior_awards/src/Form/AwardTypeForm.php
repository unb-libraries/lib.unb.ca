<?php

namespace Drupal\ior_awards\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Default form for "IOR Award type" configuration entities.
 */
class AwardTypeForm extends EntityForm {

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $award = $this->getEntity();

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $award->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $award->id(),
      '#machine_name' => [
        'exists' => [$this, 'exist'],
      ],
      '#disabled' => !$award->isNew(),
    ];

    $form['quantity'] = [
      '#type' => 'number',
      '#title' => $this->t('Quantity'),
      '#default_value' => $award->get('quantity') ?: 1,
    ];

    $form['weight'] = [
      '#type' => 'number',
      '#title' => $this->t('Weight'),
      '#default_value' => $award->get('weight') ?: 0,
    ];

    return $form;
  }

  /**
   * Helper function to check whether an award entity exists.
   */
  public function exist($id) {
    $entity_type_id = $this->getEntity()->getEntityTypeId();
    $entity = $this->entityTypeManager->getStorage($entity_type_id)->getQuery()
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

}
