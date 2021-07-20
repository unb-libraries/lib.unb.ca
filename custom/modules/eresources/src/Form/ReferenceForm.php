<?php

namespace Drupal\eresources\Form;

/**
 * KB Reference form.
 */
class ReferenceForm extends KbFormBase implements KbFormInterface {

  /**
   * {@inheritDoc}
   */
  public function getKbFormId() {
    return 'reference';
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
    return $this->t('Search for reference materials, dictionaries, encyclopedias, handbooks, etc.');
  }

  /**
   * {@inheritDoc}
   */
  public function getKbContentType() {
    return 'ref';
  }

}
