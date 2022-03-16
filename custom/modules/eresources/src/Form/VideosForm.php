<?php

namespace Drupal\eresources\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

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

    $form['videos_wrapper']['query_wrapper']['#attributes']['class'][] = 'mb-0';

    $form['videos_wrapper']['links'] = [
      '#markup' => '<div class="wrapper-list-inline item-list">
<ul>
  <li><a href="' . Url::fromRoute('eresources.collections', ['type' => 'videos'])->toString() . '">Browse Video Collections</a></li>
</ul>
</div>',
    ];

    return $form;
  }

}
