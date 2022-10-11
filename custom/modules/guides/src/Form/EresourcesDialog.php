<?php

namespace Drupal\guides\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\editor\Ajax\EditorDialogSave;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Eresources selector widget for ckeditor.
 */
class EresourcesDialog extends FormBase {

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
    return 'guides_eresources_dialog';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $userInput = $form_state->getUserInput();
    $input = $userInput['editor_object'] ?? [];

    $form['eresources_selector'] = [
      '#prefix' => '<div class="eresources-selector">',
      '#suffix' => '</div>',
    ];

    $form['eresources_selector']['eresources_local'] = [
      '#type' => 'checkbox',
      '#title' => 'Show local records',
      '#ajax' => [
        'callback' => '::loadRecords',
        'disable-refocus' => FALSE,
        'wrapper' => 'record-select',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Loading...'),
        ],
      ],
    ];

    $showLocal = !!$form_state->getValue('eresources_local');
    $options = $this->getRecords($showLocal);

    $form['eresources_selector']['eresources_search'] = [
      '#type' => 'select',
      '#title' => $this->t('e-Resources'),
      '#prefix' => '<div id="record-select">',
      '#suffix' => '</div>',
      '#options' => $options,
    ];

    $form['eresources_selector']['eresources_add'] = [
      '#type' => 'button',
      '#value' => 'Add',
      '#attributes' => [
        'class' => ['add'],
      ],
    ];

    $selected = [];
    if ($input['ids']) {
      $ids = explode(',', $input['ids']);
      $selected = $this->getRecords(TRUE, $ids);
    }
    $form['eresources_selector']['eresources_selected'] = [
      '#type' => 'select',
      '#multiple' => TRUE,
      '#size' => 10,
      '#options' => $selected,
      '#prefix' => '<div id="records">',
      '#suffix' => '</div>',
      '#attributes' => ['style' => ['width:100%;']],
    ];

    $form['eresources_selector']['ids'] = [
      '#type' => 'hidden',
      '#default_value' => $input['ids'] ?? '',
    ];

    $form['eresources_selector']['eresources_remove'] = [
      '#type' => 'button',
      '#value' => 'Remove',
      '#attributes' => [
        'class' => ['remove'],
      ],
    ];
    $form['eresources_selector']['eresources_up'] = [
      '#type' => 'button',
      '#value' => 'Up',
      '#attributes' => [
        'class' => ['up'],
      ],
    ];
    $form['eresources_selector']['eresources_down'] = [
      '#type' => 'button',
      '#value' => 'Down',
      '#attributes' => [
        'class' => ['down'],
      ],
    ];

    $form['keyresources'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of resources to designate as "Key Resources"'),
      '#default_value' => $input['keyresources'] ?? 10,
    ];

    $form['searchbox'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable WorldCat targeted database search for these resources'),
      '#default_value' => $input['searchbox'] ?? 0,
    ];

    $form['noheadings'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable headings'),
      '#default_value' => $input['noheadings'] ?? 0,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['save_modal'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#submit' => [],
      '#ajax' => [
        'callback' => '::submitForm',
        'event' => 'click',
      ],
    ];

    $form['#attached']['library'][] = 'guides/eresources';

    return $form;
  }

  /**
   * Ajax callback for eresources record list.
   */
  public function loadRecords(array &$form, FormStateInterface $form_state) {
    return $form['eresources_selector']['eresources_search'];
  }

  /**
   * Convenience function for listing eresources records.
   */
  public function getRecords($showLocal, $selected = NULL) {
    $options = [];

    $storage = $this->entityTypeManager->getStorage('eresources_record');
    $query = $storage->getQuery()
      ->condition('status', 1)
      ->sort('title', 'ASC');
    if (!empty($selected)) {
      $query->condition('id', $selected, 'IN');
    }
    elseif (!$showLocal) {
      $query->exists('entry_uid');
    }
    $ids = $query->execute();
    $records = $storage->loadMultiple($ids);
    foreach ($records as $record) {
      $id = $record->id();
      $label = "[id:{$id}; " . ($record->entry_uid->getString() ? 'KB' : 'LOCAL') . '] ' . $record->label();
      $options[$id] = $label;
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = array_intersect_key(
      $form_state->getValues(),
      array_flip(['ids', 'keyresources', 'searchbox', 'noheadings']),
    );

    // Render an HTML list for display in the editor.
    $ids = explode(',', $values['ids']);
    $storage = $this->entityTypeManager->getStorage('eresources_record');
    $resources = $storage->loadMultiple($ids);
    $html = '<p>List of resources as of ' . date('Y-m-d H:i:s') . '</p><ul>';
    foreach ($resources as $resource) {
      $html .= '<li>' . $resource->label() . '</li>';
    }
    $html .= '</ul>';
    $values['html'] = $html;

    $response = new AjaxResponse();
    $response->addCommand(new EditorDialogSave($values));
    $response->addCommand(new CloseModalDialogCommand());

    return $response;
  }

}
