<?php

namespace Drupal\guides\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the list of level2 records.
 */
class EresourcesListForm extends FormBase {

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
    return 'eresources_list';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['intro'] = [
      '#markup' => '<p>Use this tool to view resource record use within Guides or to create and edit <b>Print, eBook or Custom Local Records</b>.
<p>The custom records here are maintained by Subject Specialists (Librarians and Reference staff) and not by Cataloguing staff. Be cautious when editing records as others may also have linked them from within their guides.</p>
<p>Only local records which are unused may be deleted.</p>
<p>You may also view the usage of Cataloguing-maintained eResource Discovery records but you may not edit them.</p>',
    ];

    $form['add'] = [
      '#type' => 'link',
      '#title' => 'Add a new CUSTOM record',
      '#url' => Url::fromUri('https://example.com'),
      '#attributes' => ['class' => ['button']],
    ];

    $form['heading'] = [
      '#markup' => '<h2>Browse/Edit Existing Records</h2>',
    ];

    $form['eresources_selector'] = [
      '#prefix' => '<div class="eresources-selector">',
      '#suffix' => '</div>',
    ];

    $form['eresources_selector']['eresources_all'] = [
      '#type' => 'checkbox',
      '#title' => 'Include Cataloguing-maintained eResource records',
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

    $showAll = !!$form_state->getValue('eresources_all');
    $options = $this->getRecords($showAll);

    $form['eresources_selector']['eresources_search'] = [
      '#type' => 'select',
      '#title' => $this->t('e-Resources'),
      '#prefix' => '<div id="record-select">',
      '#suffix' => '</div>',
      '#options' => $options,
      '#attributes' => [
        'class' => ['custom-select'],
        'style' => ['width:100%;'],
      ],
      '#ajax' => [
        'callback' => '::findGuidesByRes',
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Loading...'),
        ],
      ],
    ];

    $form['result'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => ['id' => 'result'],
    ];

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
  public function getRecords($showAll) {
    $options = [];

    $storage = $this->entityTypeManager->getStorage('eresources_record');
    $query = $storage->getQuery()
      ->condition('status', 1)
      ->sort('title', 'ASC');
    if (!$showAll) {
      $query->notExists('entry_uid');
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
   * Ajax callback to find guides by level2 record.
   */
  public function findGuidesByRes(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $response->addCommand(new HtmlCommand('#result', '<p>test</p>'));
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
