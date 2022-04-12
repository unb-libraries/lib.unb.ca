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
   * {@inheritDoc}
   */
  public function getDebug() {
    $debug = '<p class="h4">KB Record Fields</p>';
    $debug .= '<ul class="mb-2">';

    $f = [
      'entry_uid', 'title', 'collection_name', 'oclcnum', 'isbn', 'issn',
      'eissn', 'url', 'author', 'publisher', 'coverage', 'coverageenum',
      'coverage_notes', 'collection_user_notes', 'location',
    ];
    foreach ($f as $k) {
      $debug .= "<li><b>{$k}:</b> " . htmlspecialchars($this->getFirstValue($k)) . '</li>';
    }

    $debug .= '</ul>';
    $debug .= '<p class="h4">Local Metadata</p>';
    $debug .= '<ul class="mb-2">';

    $f = [
      'alternate_title', 'date_coverage', 'subscription_start_date',
      'subscription_end_date', 'description', 'access_information',
      'license_status', 'is_collection',
    ];
    foreach ($f as $k) {
      $value = htmlspecialchars($this->getMetadataField($k, 'local'));
      if (!empty($value)
        && in_array($k, ['subscription_start_date', 'subscription_end_date'])) {
        $value = date('Y-m-d H:i:s', (int) $value);
      }
      $debug .= "<li><b>{$k}:</b> " . $value . '</li>';
    }

    $debug .= '</ul>';
    $debug .= '<p class="h4">OCLC Metadata</p>';
    $debug .= '<ul class="mb-2">';

    $f = ['description'];
    foreach ($f as $k) {
      $debug .= "<li><b>{$k}:</b> " . htmlspecialchars($this->getMetadataField($k, 'oclc')) . '</li>';
    }

    $debug .= '</ul>';

    return $debug;
  }

  /**
   * Returns the database ID of the record.
   */
  public function getId() {
    return $this->getFirstValue('id');
  }

  /**
   * Returns the KB data type of the record.
   */
  public function getKbDataType() {
    return $this->getFirstValue('kb_data_type');
  }

  /**
   * Return the metadata field value for a source (default "local").
   */
  public function getMetadataField($field, $source = 'local') {
    return $this->getFirstValue("metadata_{$source}_{$field}");
  }

  /**
   * Return the first metadata value found, iterating through sources.
   */
  public function getFirstMetadataField($field, $sources = ['local', 'oclc']) {
    foreach ($sources as $source) {
      $value = $this->getMetadataField($field, $source);
      if (!empty($value)) {
        return $value;
      }
    }

    return NULL;
  }

  /**
   * Return the first field value from the index, or NULL.
   */
  private function getFirstValue($field) {
    $itemField = $this->item->getField($field);
    if (empty($itemField)) {
      return NULL;
    }
    $values = $itemField->getValues();
    $result = $values[0] ?? NULL;
    if (!is_null($result)) {
      $result = (string) $result;
    }
    return $result;
  }

}
