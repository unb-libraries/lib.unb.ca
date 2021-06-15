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
