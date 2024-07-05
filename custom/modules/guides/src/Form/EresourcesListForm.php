<?php

namespace Drupal\guides\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
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
    if (empty($form_state->getUserInput())) {
      $message = Markup::Create('<p>Use this tool to view resource record use within Guides or to create and edit <b>Print, eBook or Custom Local Records</b>.
<p>The custom records here are maintained by Subject Specialists (Librarians and Reference staff) and not by Cataloguing staff. Be cautious when editing records as others may also have linked them from within their guides.</p>
<p>Only local records which are unused may be deleted.</p>
<p>You may also view the usage of Cataloguing-maintained eResource Discovery records but you may not edit them.</p>');

      $this->messenger()->addMessage($message);
    }

    $form['add'] = [
      '#type' => 'link',
      '#title' => 'Add a new LOCAL record',
      '#url' => Url::fromRoute('guides.local_eresource.add'),
      '#attributes' => ['class' => ['button']],
    ];

    $form['heading'] = [
      '#markup' => '<h2>Browse/Edit Existing Records</h2>',
    ];

    $form['selector'] = [
      '#prefix' => '<div class="eresources-selector">',
      '#suffix' => '</div>',
    ];

    $options = $this->getRecords();

    $form['selector']['search'] = [
      '#type' => 'select',
      '#title' => $this->t('e-Resources'),
      '#prefix' => '<div id="record-select">',
      '#suffix' => '</div>',
      '#options' => $options,
      '#empty_value' => '',
      '#attributes' => [
        'class' => ['form-control', 'selectize', 'selectize-control'],
      ],
    ];

    $id = $this->getRequest()->query->get('id');
    if (!empty($id) && array_key_exists($id, $options)) {
      $form['selector']['search']['#default_value'] = $id;
    }

    $form['selector']['view'] = [
      '#type' => 'button',
      '#value' => 'View',
      '#ajax' => [
        'callback' => '::refreshRecordInfo',
        'wrapper' => 'result',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Loading...'),
        ],
      ],
    ];

    $form['result'] = [
      '#prefix' => '<div id="result">',
      '#suffix' => '</div>',
      '#markup' => $this->recordInfo($form, $form_state),
    ];

    $form['#attached']['library'][] = 'lib_core/lib-selectize';
    $form['#attached']['library'][] = 'guides/guides-selectize';
    $form['#attached']['library'][] = 'guides/guides-admin';
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
  public function getRecords() {
    $options = [];

    $storage = $this->entityTypeManager->getStorage('eresources_record');
    $query = $storage->getQuery()
      ->condition('status', 1)
      ->sort('title', 'ASC');

    $ids = $query->execute();
    $records = $storage->loadMultiple($ids);
    foreach ($records as $record) {
      $id = $record->id();
      $label = ($record->is_local->getString() ? '[LOCAL] ' : '') . $record->label() . ' <' . $record->kb_data_type->getString() . '>';
      $options[$id] = $label;
    }

    return $options;
  }

  /**
   * Load record info.
   */
  public function recordInfo(array &$form, FormStateInterface $form_state) {
    $id = $form_state->getValue('search');
    if (is_null($id)) {
      return;
    }
    if (!empty($form['selector']['search']['#default_value'])) {
      $id = $form['selector']['search']['#default_value'];
    }

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

      if ($record->is_local->getString()) {
        $editUrl = Url::fromRoute('guides.local_eresource.edit', ['id' => $id])->toString();
        $text .= '<a class="button" href="' . $editUrl . '">Edit Record</a>';
        if (empty($links)) {
          $deleteUrl = Url::fromRoute('guides.local_eresource.delete', ['id' => $id])->toString();
          $text .= ' <a class="button" href="' . $deleteUrl . '">Delete Record</a>';
        }
        $text .= '<p class="local">[LOCAL] This record has been <b>added manually</b> by a Guide Editor and <b>may need review</b>.</p>';
        $text .= '<ul><li>URL or eBook link: ' . $record->url->getString() . ($record->license_status->getString() == 'Y' ? ' (Licensed Resource)' : '') . '</li>';
        $text .= '<li>Physical items:<ul>';
        $text .= '  <li>Shelving Location: ' . $record->catalogue_location->getString() . '</li>';
        $text .= '  <li>Call Number: ' . $record->call_number->getString() . '</li>';
        $text .= '  <li>OCLC Number: ' . $record->oclcnum->getString() . '</li>';
        $text .= '</ul></li></ul>';
      }
      else {
        $text .= '<p class="catalogue">This record is <b>maintained by Cataloguing</b> and is part of eResources Discovery.</p>';
        if ($this->currentUser()->hasPermission('update eresources_record entities')) {
          $recordUrl = Url::fromRoute('entity.eresources_record.canonical', ['eresources_record' => $id])->toString();
          $text .= '<a class="button" href="' . $recordUrl . '">View Record</a>';
        }
      }

      if (!empty($record->description->getString())) {
        $render = [
          '#type' => 'processed_text',
          '#text' => $record->description->value,
          '#format' => $record->description->format,
        ];
        $description = \Drupal::service('renderer')->render($render);
        $text .= '<blockquote>' . $description . '</blockquote>';
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
        if (!$guide) {
          $text .= '<li>[Deleted Guide: ID #' . $link->get('guide')->getString() . ']</li>';
        }
        else {
          $label = $guide->label();
          $url = $guide->toUrl()->toString();
          $sectionId = $link->get('section')->getString();
          $section = $this->entityTypeManager->getStorage('paragraph')->load($sectionId)->field_section_label->getString();
          $text .= "<li><a href=\"{$url}#section-{$sectionId}\" target=\"_blank\">$label</a> <span>({$section})</span></li>";
        }
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
