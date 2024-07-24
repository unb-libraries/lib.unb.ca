<?php

namespace Drupal\eresources\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * KB Reference form.
 */
class ReferenceForm extends LocalFormBase implements KbFormInterface {

  /**
   * {@inheritDoc}
   */
  public function getKbFormId() {
    return 'reference';
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
          'mb-0',
        ],
      ],
      '#weight' => 0,
    ];

    $storage = \Drupal::entityTypeManager()->getStorage('guide_category');
    $query = $storage->getQuery();
    $ids = $query->sort('title')->execute();
    $categoryList = $storage->loadMultiple($ids);

    $categories = [];
    foreach ($categoryList as $category) {
      $categories[$category->toUrl()->toString()] = $category->label();
    }

    $guideOptions = ['' => '- Select a Subject -'] + $categories;
    $form[$form_wrapper]['guide_wrapper']['guide'] = [
      '#title' => 'Browse Reference Materials by subject',
      '#type' => 'select',
      '#options' => $guideOptions,
      '#attributes' => [
        'class' => [
          'selectize',
          'form-control',
        ],
        'name' => '',
        'id' => 'reference-guide',
      ],
      '#option_attributes' => [
        0 => ['aria-disabled' => 'true'],
      ],
      '#weight' => 0,
    ];

    $form[$form_wrapper]['guide_wrapper']['actions'] = [
      '#type' => 'actions',
      '#weight' => 0,
    ];

    $form[$form_wrapper]['guide_wrapper']['actions']['submit_button'] = [
      '#type' => 'html_tag',
      '#tag' => 'input',
      '#attributes' => [
        'class' => [
          'btn',
          'btn-primary',
          'form-control',
        ],
        'id' => 'reference-guide-submit',
        'type' => 'button',
        'value' => $this->t('GO'),
      ],
    ];

    $form[$form_wrapper]['links_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'border-bottom',
          'border-dark',
          'pb-3',
        ],
      ],
      '#weight' => 0,
    ];
    $form[$form_wrapper]['links_wrapper']['links'] = [
      '#markup' => '<div class="wrapper-list-inline item-list">
<ul>
  <li><a href="' . Url::fromRoute('eresources.collections', ['type' => 'references'])->toString() . '">Browse Reference Collections</a></li>
  <li><a href="https://lib.unb.ca/eresources/guide-finding-reference-materials" title="Guide to finding Reference Materials at UNB Libraries"> Reference Materials Guide</a></li>
  <li><a href="https://lib.unb.ca/guides/dictionaries"> Browse Dictionaries</a></li>
</ul>
</div>',
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
    return 'e-Reference Materials';
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchPlaceholder() {
    return $this->t('Search for reference materials, dictionaries, encyclopedias, handbooks, etc.');
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchDescription() {
    return $this->t('Search for reference materials, dictionaries, encyclopedias, handbooks, etc.');
  }

  /**
   * {@inheritDoc}
   */
  public function getKbContentType() {
    return 'REF';
  }

}
