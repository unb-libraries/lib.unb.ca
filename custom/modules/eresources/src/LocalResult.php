<?php

namespace Drupal\eresources;

/**
 * Local result item.
 */
class LocalResult extends ResultBase implements ResultInterface {

  /**
   * {@inheritDoc}
   */
  public function getResultType() {
    return 'local';
  }

  /**
   * {@inheritDoc}
   */
  public function getTitle() {
    return $this->getFirstValue('title');
  }

  /**
   * {@inheritDoc}
   */
  public function getCollectionName() {
    return $this->getFirstValue('collection_name');
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
    return $this->getFirstValue('isbn');
  }

  /**
   * {@inheritDoc}
   */
  public function getIssn() {
    return $this->getFirstValue('issn');
  }

  /**
   * {@inheritDoc}
   */
  public function getEissn() {
    return $this->getFirstValue('eissn');
  }

  /**
   * {@inheritDoc}
   */
  public function getViaUrl() {
    return $this->getFirstValue('url');
  }

  /**
   * {@inheritDoc}
   */
  public function getAuthor() {
    return $this->getFirstValue('author');
  }

  /**
   * {@inheritDoc}
   */
  public function getPublisher() {
    return $this->getFirstValue('publisher');
  }

  /**
   * {@inheritDoc}
   */
  public function getCoverage() {
    return $this->getFirstValue('coverage');
  }

  /**
   * {@inheritDoc}
   */
  public function getCoverageEnum() {
    return $this->getFirstValue('coverageenum');
  }

  /**
   * {@inheritDoc}
   */
  public function getCoverageNotes() {
    return $this->getFirstValue('coverage_notes');
  }

  /**
   * {@inheritDoc}
   */
  public function getCollectionUserNotes() {
    return $this->getFirstValue('collection_user_notes');
  }

  /**
   * {@inheritDoc}
   */
  public function getLocation() {
    return $this->getFirstValue('location');
  }

  /**
   * Returns the database ID of the record.
   */
  public function getId() {
    return $this->getFirstValue('id');
  }

  /**
   * Return the first field value from the index, or NULL.
   */
  private function getFirstValue($field) {
    $values = $this->item->getField($field)->getValues();
    $result = $values[0] ?? NULL;
    if (!is_null($result)) {
      $result = (string) $result;
    }
    return $result;
  }

}
