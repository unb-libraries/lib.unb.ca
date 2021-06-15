<?php

namespace Drupal\eresources\Form;

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
  public function getKbContentType() {
    return 'ebook';
  }

}
