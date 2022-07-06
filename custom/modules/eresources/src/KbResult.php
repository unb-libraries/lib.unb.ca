<?php

namespace Drupal\eresources;

/**
 * KB result item.
 */
class KbResult extends ResultBase implements ResultInterface {

  /**
   * {@inheritDoc}
   */
  public function getResultType() {
    return 'kb';
  }

  /**
   * {@inheritDoc}
   */
  public function getTitle() {
    return $this->item->title;
  }

  /**
   * {@inheritDoc}
   */
  public function getCollectionName() {
    return $this->item->{'kb:collection_name'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getOclcNum() {
    return $this->item->{'kb:oclcnum'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getIsbn() {
    return $this->item->{'kb:isbn'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getIssn() {
    return $this->item->{'kb:issn'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getEissn() {
    return $this->item->{'kb:eissn'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getViaUrl() {
    foreach ($this->item->links as $link) {
      if ($link->rel == 'via') {
        return $link->href;
      }
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getAuthor() {
    return $this->item->{'kb:author'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getPublisher() {
    $result = $this->item->{'kb:publisher'} ?? NULL;
    if (is_null($result)) {
      return NULL;
    }

    return preg_replace('/[,\s]+$/', '', $result);
  }

  /**
   * {@inheritDoc}
   */
  public function getCoverage() {
    return $this->item->{'kb:coverage'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getCoverageEnum() {
    return $this->item->{'kb:coverage_enum'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getCoverageNotes() {
    return $this->item->{'kb:coverage_notes'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getCollectionUserNotes() {
    return $this->item->{'kb:collection_user_notes'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getLocation() {
    return $this->item->{'kb:location'} ?? NULL;
  }

  /**
   * Returns the KB entry ID of the record.
   */
  public function getId() {
    return $this->item->{'kb:entry_uid'} ?? NULL;
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
      $class = '';
      if ($k == 'url') {
        $class = ' class="text-break"';
      }
      $value = $this->item->{"kb:{$k}"} ?? '';
      $debug .= "<li{$class}><b>{$k}:</b> " . htmlspecialchars($value) . '</li>';
    }

    $debug .= '</ul>';

    return $debug;
  }

}
