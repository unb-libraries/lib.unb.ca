<?php

namespace Drupal\guides\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form for custom local eresources.
 */
class EresourcesLocalRecordForm extends FormBase {

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
    $id = $this->getRequest()->query->get('id');
    if (!empty($id)) {
      return 'edit_local_eresource';
    }
    return 'add_local_eresource';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    $warning = Markup::Create('<b>Cataloguing-maintained eResource records</b><br>
The updated eResources Discovery system now includes over 1500 titles from Wiley/Blackwell Reference, Oxford Reference/Bibliographies, Gale Virtual  Reference and Cambridge Histories.<br><br>
<b>We strongly encourage</b> you to use these maintained records instead of adding new ones and convert any old custom record selections to these. This will ensure any updates or URL changes are made for you.<br><br>
<b>Local Record Display</b><br>
Local custom records are shared across all guides that are currently linked to them. Please keep this in mind when editing. Others may depend on them.<br><br>
Reach out to <a href="https://lib.unb.ca/help/ticket/new?nature=Other&title=eResources%20in%20Research%20Guides" target="_blank">Scott Shannon</a> if you need help with selections or there are issues with records.');

    if (empty($form_state->getUserInput())) {
      $this->messenger()->addWarning($warning);
    }

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => 'Title',
      '#required' => TRUE,
      '#maxlength' => 256,
    ];

    $form['description'] = [
      '#type' => 'text_format',
      '#title' => 'Brief Description',
      '#required' => TRUE,
      '#format' => 'library_page_html',
    ];

    $form['kb_data_type'] = [
      '#type' => 'select',
      '#title' => 'Resource Type',
      '#options' => [
        'REF,PHYS' => $this->t('Reference Source'),
        'DATA,PHYS' => $this->t('Article Database'),
      ],
      '#description' => $this->t('Resources are available in all tabs.'),
    ];

    $form['online'] = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => 'For online resources',
    ];

    $form['url'] = [
      '#type' => 'textfield',
      '#title' => 'e-Book Link or URL',
      '#description' => $this->t('Will hyperlink the title to the value in this field. Omit the proxy prefix.'),
      '#maxlength' => 256,
    ];

    $form['license_status'] = [
      '#type' => 'checkbox',
      '#title' => 'This is a licensed resource',
      '#description' => $this->t('The proxy prefix will be added automatically.'),
      '#return_value' => 'Y',
    ];

    $form['print'] = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => 'For print resources',
    ];

    $form['catalogue_location'] = [
      '#type' => 'textfield',
      '#title' => 'Catalogue Location',
      '#description' => $this->t('eg. HIL-REF. If you want this represented as multiple locations, enter "multiple locations" and enter the search parameter in the OCLC Number field.'),
      '#maxlength' => 256,
    ];

    $form['call_number'] = [
      '#type' => 'textfield',
      '#title' => 'Call Number',
      '#description' => $this->t('eg. GN316 .R37 2000'),
      '#maxlength' => 256,
    ];

    $form['oclcnum'] = [
      '#type' => 'number',
      '#title' => 'OCLC Number',
      '#description' => $this->t('eg. 69734537. Enter ONLY one with no spaces or hyphens. This is an OCLC Number search into WorldCat.'),
      '#maxlength' => 256,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];

    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
      '#weight' => 5,
      '#submit' => ['::cancelForm'],
      '#limit_validation_errors' => [],
    ];

    if ($id) {
      $form['id'] = [
        '#type' => 'hidden',
        '#value' => $id,
      ];
      $form['actions']['submit']['#value'] = $this->t('Update');

      $storage = $this->entityTypeManager->getStorage('eresources_record');
      $record = $storage->load($id);

      $fields = ['title', 'kb_data_type', 'url', 'oclcnum'];
      foreach ($fields as $field) {
        $form[$field]['#default_value'] = $record->get($field)->getString();
      }

      $localMetadata = $record->local_metadata_id->entity;

      $fields = [
        'license_status', 'catalogue_location', 'call_number',
      ];
      foreach ($fields as $field) {
        $form[$field]['#default_value'] = $localMetadata->get($field)->getString();
      }

      $description = $localMetadata->description->getValue()[0];
      $form['description']['#default_value'] = $description['value'];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('catalogue_location') == 'multiple locations') {
      return;
    }

    $fields = ['catalogue_location', 'call_number', 'oclcnum'];
    $filled = [];
    foreach ($fields as $f) {
      if (!empty($form_state->getValue($f))) {
        $filled[] = $f;
      }
    }
    if (count($filled) != 0 && count($filled) != 3) {
      foreach (array_diff($fields, $filled) as $m) {
        $form_state->setErrorByName($m, $this->t('All of "Location", "Call Number" and "OCLC Number" must be entered or none at all.'));
      }
    }
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
    $id = $form_state->getValue('id');
    if ($id) {
      $storage = $this->entityTypeManager->getStorage('eresources_record');
      $record = $storage->load($id);
      $mode = 'updated';
    }
    else {
      $storage = $this->entityTypeManager->getStorage('eresources_record');
      $record = $storage->create();
      $record->set('is_local', TRUE);
      $mode = 'created';
    }

    $fields = [
      'title', 'kb_data_type', 'url', 'oclcnum',
      'description', 'license_status', 'catalogue_location', 'call_number',
    ];
    foreach ($fields as $field) {
      $record->set($field, $form_state->getValue($field));
    }
    $record->save();

    $form_state->setRedirect('guides.eresources_list', [], ['query' => ['id' => $record->id]]);
    $this->messenger()->addStatus("Record $mode");
    return;
  }

}
