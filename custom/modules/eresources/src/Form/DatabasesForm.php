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
    $options = parent::getSearchOptions();
    $options['keyword'] = 'Keyword search (title, description...)';
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form_wrapper = $this->getKbFormId() . "_wrapper";

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
        'id' => 'database-guide',
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
      '#type' => 'button',
      '#value' => $this->t('GO'),
      '#attributes' => [
        'class' => [
          'btn',
          'btn-primary',
          'form-control',
        ],
        'id' => 'database-guide-submit',
        'type' => 'submit',
      ],
    ];

    $form[$form_wrapper]['database_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'form-row',
          'flex-sm-nowrap',
          'border-top',
          'border-bottom',
          'border-dark',
          'pt-4',
          'pb-3',
        ],
      ],
      '#weight' => 0,
    ];

    $index = Index::load('eresources');
    $indexQuery = $index->query();
    $parseMode = \Drupal::service('plugin.manager.search_api.parse_mode')->createInstance('direct');
    $indexQuery->setParseMode($parseMode);

    $indexQuery->addCondition('kb_data_type', $this->getKbContentType());
    $indexQuery->sort('title', 'ASC');
    $indexQuery->keys('*');
    $indexQuery->range(0, 10000);
    $result = $indexQuery->execute();

    $options = ['' => '- Select a Database -'];
    $entries = $result->getResultItems();
    foreach ($entries as $entry) {
      $id = $entry->getField('id')->getValues()[0];
      $options["id:{$id}"] = $entry->getField('title')->getValues()[0];
    }

    $form[$form_wrapper]['database_wrapper']['database'] = [
      '#title' => '<span class="text-danger">OR</span> Browse for Databases by title',
      '#type' => 'select',
      '#options' => $options,
      '#attributes' => [
        'class' => [
          'custom-chosen-select',
          'form-control',
        ],
        'id' => 'database',
        'name' => '',
      ],
    ];

    $form[$form_wrapper]['database_wrapper']['actions'] = [
      '#type' => 'actions',
    ];

    $form[$form_wrapper]['database_wrapper']['actions']['submit_button'] = [
      '#type' => 'button',
      '#value' => $this->t('GO'),
      '#attributes' => [
        'class' => [
          'btn',
          'btn-primary',
          'form-control',
        ],
        'id' => 'database-submit',
        'type' => 'submit',
      ],
    ];

    $form[$form_wrapper]['query_wrapper']['query']['#title_display'] = 'invisible';
    $form[$form_wrapper]['type']['#prefix'] = '<span class="h6"><span class="text-danger">OR</span> Search</span>';
    $form[$form_wrapper]['query_wrapper']['query']['#required'] = FALSE;

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
    return $this->t('Search for databases by title, description, etc.');
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
