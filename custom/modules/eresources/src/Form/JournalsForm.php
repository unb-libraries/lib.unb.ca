<?php

namespace Drupal\eresources\Form;

/**
 * KB Journals form.
 */
class JournalsForm extends KbFormBase implements KbFormInterface {

  /**
   * {@inheritDoc}
   */
  public function getKbFormId() {
    return 'journals';
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
   * {@inheritDoc}
   */
  public function getSearchPlaceholder() {
    return $this->t('Enter 1 or more search terms');
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchDescription() {
    return $this->t('Search for individual journals, newspapers and conference proceedings by title.');
  }

  /**
   * {@inheritDoc}
   */
  public function getKbContentType() {
    return 'fulltext,print';
  }

}
