<?php

namespace Drupal\eresources;

/**
 * Local result item.
 */
class LocalResult extends ResultBase implements ResultInterface {

  /**
   * {@inheritDoc}
   */
  public function getTitle() {
    $values = $this->item->getField('title')->getValues();
    return $values[0];
  }

  /**
   * {@inheritDoc}
   */
  public function getCollectionName() {
    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getOcn() {
    $values = $this->item->getField('ocn')->getValues();
    return $values[0];
  }

  /**
   * {@inheritDoc}
   */
  public function getIsbn() {
    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getIssn() {
    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getEissn() {
    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getViaUrl() {
    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getAuthor() {
    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getPublisher() {
    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getCoverage() {
    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getCoverageEnum() {
    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getCoverageStatement() {
    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getPermittedUseStatement() {
    return NULL;
  }

}
