<?php

namespace Drupal\guides\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
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

    $form['selector'] = [
      '#prefix' => '<div class="eresources-selector">',
      '#suffix' => '</div>',
    ];

    $form['selector']['all'] = [
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

    $showAll = !!$form_state->getValue('all');
    $options = $this->getRecords($showAll);

    $form['selector']['search'] = [
      '#type' => 'select',
      '#title' => $this->t('e-Resources'),
      '#prefix' => '<div id="record-select">',
      '#suffix' => '</div>',
      '#options' => $options,
      '#attributes' => [
        'class' => ['form-control', 'selectize', 'selectize-control'],
      ],
      '#ajax' => [
        'callback' => '::refreshRecordInfo',
        'wrapper' => 'result',
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Loading...'),
        ],
      ],
    ];

    $form['result'] = [
      '#prefix' => '<div id="result">',
      '#suffix' => '</div>',
      '#markup' => $this->recordInfo($form_state),
    ];

    $form['#attached']['library'][] = 'lib_core/lib-selectize';
    $form['#attached']['library'][] = 'guides/guides-selectize';
    return $form;
  }

  /**
   * Ajax callback for eresources record list.
   */
  public function loadRecords(array &$form, FormStateInterface $form_state) {
    return $form['selector']['search'];
  }

  /**
   * Convenience function for listing eresources records.
   */
  public function getRecords($showAll) {
    $options = [
      0 => '[id:0] Deleted Resource',
    ];

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
   * Load record info.
   */
  public function recordInfo(FormStateInterface $form_state) {
    $id = $form_state->getValue('search');

    $linkStorage = $this->entityTypeManager->getStorage('eresources_guide_link');
    $query = $linkStorage->getQuery()
      ->sort('guide.entity.title');
    if ($id) {
      $query->condition('eresource', $id);
    }
    else {
      $orGroup = $query->orConditionGroup()
        ->condition('eresource', 0)
        ->notExists('eresource');
      $query->condition($orGroup);
    }

    $linkIds = $query->execute();
    $links = $linkStorage->loadMultiple($linkIds);

    if ($id) {
      $recordStorage = $this->entityTypeManager->getStorage('eresources_record');
      $record = $recordStorage->load($id);
      $text = '<h2>' . $record->label() . " <span class=\"text-muted small\">[id:{$id}]</span></h2>";

      if ($record->entry_uid->getString()) {
        $text .= '<p>This record is <b>maintained by Cataloguing</b> and is part of eResources Discovery.</p>';
      }
      else {
        $text .= '<p>This record has been <b>added manually</b> by a Guide Editor and <b>may need review</b>.</p>';
      }
    }
    else {
      $text = '<h2>Deleted Resource <span class="text-muted small">[id:0]</span></h2>';
    }

    if (empty($links)) {
      $text .= '<p>This record is not used in any guides.</p>';
    }
    else {
      $text .= '<p>Record found in the following guides:</p>';
      $text .= '<ul>';
      foreach ($links as $link) {
        $guide = $link->get('guide')->entity;
        $label = $guide->label();
        $url = $guide->toUrl()->toString();
        $sectionId = $link->get('section')->getString();
        $section = $this->entityTypeManager->getStorage('paragraph')->load($sectionId)->field_section_label->getString();
        $text .= "<li><a href=\"{$url}#section-{$sectionId}\" target=\"_blank\">$label</a> <span>({$section})</span></li>";
      }
      $text .= '</ul>';
    }

    return $text;
  }

  /**
   * Ajax callback for record info.
   */
  public function refreshRecordInfo(array &$form, FormStateInterface $form_state) {
    return $form['result'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
