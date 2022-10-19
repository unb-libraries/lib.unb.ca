<?php

namespace Drupal\guides\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;

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
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   An entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'is_subject_guide_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, int $guideId = NULL) {
    $this->guide = $this->entityTypeManager->getStorage('guide')->load($guideId);

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
