<?php

namespace Drupal\guides\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Ajax form to set is_subject_guide field.
 */
class IsSubjectGuideForm extends FormBase {

  /**
   * Guide.
   *
   * @var \Drupal\entity\ContentEntityInterface
   */
  private $guide;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'is_subject_guide_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, EntityInterface $guide = NULL) {
    $this->guide = $guide;

    $form['is_subject_guide_metadata'] = [
      '#type' => 'container',
    ];

    $form['is_subject_guide_metadata']['course_guide'] = [
      '#title' => $this->t('This is a course or cross-listed course guide.'),
      '#type' => 'checkbox',
      '#default_value' => !$this->guide->is_subject_guide->value,
      '#ajax' => [
        'callback' => '::submitForm',
        'event' => 'change',
        'effect' => 'fade',
        'speed' => 'slow',
      ],
    ];

    $form['is_subject_guide_metadata']['status'] = [
      '#type' => 'markup',
      '#markup' => '<div id="save-status"></div>',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $guide = $this->guide;

    $guide->set('is_subject_guide', !$form_state->getValue('course_guide'));
    $guide->save();

    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand("#save-status", '<div id="save-status">Saved.</div>'));

    return $response;
  }

}
