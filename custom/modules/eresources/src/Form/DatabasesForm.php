<?php

namespace Drupal\eresources\Form;

/**
 * KB Databases form.
 */
class DatabasesForm extends KbFormBase implements KbFormInterface {

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
   * {@inheritDoc}
   */
  public function getSearchDescription() {
    return $this->t('Use article databases to find articles, reviews, book chapters, etc.');
  }

  /**
   * {@inheritDoc}
   */
  public function getKbContentType() {
    return 'data,jour';
  }

}
