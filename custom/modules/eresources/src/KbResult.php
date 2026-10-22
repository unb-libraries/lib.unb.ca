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

    foreach (get_object_vars($this->item) as $k => $value) {
      if ($k == 'links') {
        foreach ($value as $link) {
          $debug .= "<li class=\"text-break\"><b>links ({$link->rel}):</b> " . htmlspecialchars($link->href) . '</li>';
        }
        continue;
      }
      if (!is_scalar($value)) {
        $value = json_encode($value);
      }
      $class = '';
      if ($k == 'id') {
        $class = ' class="text-break"';
      }
      $debug .= "<li{$class}><b>{$k}:</b> " . htmlspecialchars($value) . '</li>';
    }

    $debug .= '</ul>';

    return $debug;
  }

}
