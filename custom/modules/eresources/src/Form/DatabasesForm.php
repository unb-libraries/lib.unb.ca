<?php

namespace Drupal\eresources\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\search_api\Entity\Index;

/**
 * KB Databases form.
 */
class DatabasesForm extends LocalFormBase implements KbFormInterface {

  /**
   * {@inheritDoc}
   */
  public function getKbFormId() {
    return 'databases';
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchOptions() {
    return [
      'title' => 'Word(s) in title',
      'browse' => 'Title starts with',
      'exact' => 'Exact title',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form_wrapper = $this->getKbFormId() . "_wrapper";
    unset($form[$form_wrapper]['type']);

    $form[$form_wrapper]['guide_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'form-row',
          'flex-sm-nowrap',
        ],
      ],
      '#weight' => 0,
    ];

    $guideOptions = ['' => '- Select a Subject -'] + _lib_core_get_guide_categories();
    $form[$form_wrapper]['guide_wrapper']['guide'] = [
      '#title' => 'Browse for Databases by subject',
      '#type' => 'select',
      '#options' => $guideOptions,
      '#attributes' => [
        'class' => [
          'custom-chosen-select',
          'form-control',
        ],
        'name' => '',
        'id' => 'guide',
      ],
      '#option_attributes' => [
        0 => ['aria-disabled' => 'true'],
      ],
      '#weight' => 0,
    ];

    $form[$form_wrapper]['guide_wrapper']['actions'] = [
      '#type' => 'actions',
    ];

    $form[$form_wrapper]['guide_wrapper']['actions']['submit_button'] = [
      '#type' => 'submit',
      '#value' => $this->t('GO'),
      '#name' => '',
      '#attributes' => [
        'id' => 'guide-submit',
      ],
    ];

    $index = Index::load('eresources');
    $indexQuery = $index->query();
    $parseMode = \Drupal::service('plugin.manager.search_api.parse_mode')->createInstance('direct');
    $indexQuery->setParseMode($parseMode);

    $indexQuery->addCondition('kb_data_type', $this->getKbContentType());
    $indexQuery->sort('title', 'ASC');
    $indexQuery->keys('*');
    $result = $indexQuery->execute();

    $options = ['' => '- Select a Database -'];
    $entries = $result->getResultItems();
    foreach ($entries as $entry) {
      $id = $entry->getField('id')->getValues()[0];
      $options["id:{$id}"] = $entry->getField('title')->getValues()[0];
    }

    $req = $this->getRequest()->query;
    $query = $req->get('query');
    $form[$form_wrapper]['query_wrapper']['query'] = [
      '#title' => 'Browse for Databases by title',
      '#type' => 'select',
      '#options' => $options,
      '#value' => $query,
      '#attributes' => [
        'class' => [
          'custom-chosen-select',
          'form-control',
        ],
        'id' => 'query',
        'name' => 'query',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public static function getTitle() {
    return 'Databases';
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchPlaceholder() {
    return $this->t('Search for databases by title');
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchDescription() {
    return $this->t('Use article databases to find articles, reviews, book chapters, etc.');
  }

  /**
   * {@inheritDoc}
   */
  public function getKbContentType() {
    return 'DATA';
  }

}
