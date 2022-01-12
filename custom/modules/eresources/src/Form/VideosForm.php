<?php

namespace Drupal\eresources\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * KB Videos Form.
 */
class VideosForm extends KbFormBase implements KbFormInterface {

  /**
   * {@inheritDoc}
   */
  public function getKbFormId() {
    return 'videos';
  }

  /**
   * {@inheritDoc}
   */
  public static function getTitle() {
    return 'Videos';
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchDescription() {
    return $this->t('Search across our video collections, including VHS/DVD titles.');
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchPlaceholder() {
    return $this->t('Search for video titles');
  }

  /**
   * {@inheritDoc}
   */
  public function getKbContentType() {
    return 'video,other';
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

      $form[$form_wrapper]['collections_wrapper']['collections'] = [
        '#markup' => '<p class="font-weight-bold"><span class="text-danger">OR</span> Browse for Video Collections</p>',
      ];
    }

    return $form;
  }

}
