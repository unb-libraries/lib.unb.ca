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
  public function getOclcNum() {
    return $this->getFirstValue('oclcnum');
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
    $values = $this->item->getField('url')->getValues();
    return $values[0];
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
    $values = $this->item->getField('publisher')->getValues();
    return $values[0];
  }

  /**
   * {@inheritDoc}
   */
  public function getCoverage() {
    $values = $this->item->getField('date_coverage')->getValues();
    return $values[0];
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

  /**
   * Return the first field value from the index, or NULL.
   */
  private function getFirstValue($field) {
    $values = $this->item->getField($field)->getValues();
    return $values[0] ?? NULL;
  }

}
