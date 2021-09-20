<?php

namespace Drupal\eresources\Form;

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

}
