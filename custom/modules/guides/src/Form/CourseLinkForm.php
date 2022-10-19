<?php

namespace Drupal\guides\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Default form for "course_link" entities.
 */
class CourseLinkForm extends ContentEntityForm {

  /**
   * Guide ID.
   *
   * @var int
   */
  private $guide;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, int $guide = NULL) {
    $this->guide = $guide;

    $form = parent::buildForm($form, $form_state);

    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
      '#weight' => 5,
      '#submit' => ['::cancelForm'],
      '#limit_validation_errors' => [],
    ];

    return $form;
  }

  /**
   * Cancel the add/edit and return to course link list.
   */
  public function cancelForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('entity.course_link.collection',
      [
        'guide' => $this->guide,
      ]);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('entity.course_link.collection',
      [
        'guide' => $this->guide,
      ]);
    parent::submitForm($form, $form_state);
  }

  /**
   * Assign guide, if not already assigned.
   */
  protected function prepareEntity() {
    parent::prepareEntity();
    if (!$this->entity->guide->entity) {
      $this->entity->set('guide', $this->guide);
    }
  }

}
