<?php

namespace Drupal\guides\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form for deleting custom local eresources.
 */
class EresourcesLocalRecordDeleteForm extends FormBase {

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
    return 'delete_local_eresource';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    $form['id'] = [
      '#type' => 'hidden',
      '#value' => $id,
    ];

    $recordStorage = $this->entityTypeManager->getStorage('eresources_record');
    $record = $recordStorage->load($id);

    $form['confirm'] = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => 'Are you sure you want to delete the Local e-Resource Record "<em>' . $record->label() . '</em>" ?',
    ];
    $form['notice'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => 'This action cannot be undone.',
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete'),
      '#button_type' => 'primary',
    ];

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
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * Cancel the add/edit and return to course link list.
   */
  public function cancelForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('guides.eresources_list');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $storage = $this->entityTypeManager->getStorage('eresources_record');
    $record = $storage->load($form_state->getValue('id'));
    $record->delete();

    $form_state->setRedirect('guides.eresources_list');
    $this->messenger()->addStatus('Record deleted');
  }

}
