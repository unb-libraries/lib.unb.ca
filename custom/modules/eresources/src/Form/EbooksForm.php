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

    $req = $this->getRequest()->query;
    $query = $req->get('query');
    if (empty($query) || $this->getFormId() != $req->get('form_id')) {
      $form_wrapper = $this->getKbFormId() . "_wrapper";

      $form[$form_wrapper]['collections_wrapper'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'form-row',
            'flex-sm-nowrap',
            'border-top',
            'border-dark',
            'pt-4',
            'pb-3',
          ],
        ],
      ];

      $collectionsLink = Url::fromRoute('eresources.collections', ['type' => 'ebooks'])->toString();
      $form[$form_wrapper]['collections_wrapper']['collections'] = [
        '#markup' => '<p class="font-weight-bold mb-0"><span class="text-danger">OR</span> <a href="' . $collectionsLink . '">Browse for eBooks Collections</a></p>',
      ];
    }

    return $form;
  }

}
