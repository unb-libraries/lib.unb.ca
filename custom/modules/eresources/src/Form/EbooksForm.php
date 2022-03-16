<?php

namespace Drupal\eresources\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * KB Ebooks form.
 */
class EbooksForm extends KbFormBase implements KbFormInterface {

  /**
   * {@inheritDoc}
   */
  public function getKbFormId() {
    return 'ebooks';
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchDescription() {
    return $this->t('Search our vast electronic book collections for titles suitable for your computer, tablet or eReader.');
  }

  /**
   * {@inheritDoc}
   */
  public static function getTitle() {
    return 'e-Books';
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchPlaceholder() {
    return $this->t('Search for e-book titles');
  }

  /**
   * {@inheritDoc}
   */
  public function getKbContentType() {
    return 'ebook';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form['ebooks_wrapper']['query_wrapper']['#attributes']['class'][] = 'mb-0';

    $form['ebooks_wrapper']['links'] = [
      '#markup' => '<div class="wrapper-list-inline item-list">
<ul>
  <li><a href="' . Url::fromRoute('eresources.collections', ['type' => 'ebooks'])->toString() . '">Browse e-Book Collections</a></li>
</ul>
</div>',
    ];

    return $form;
  }

}
