<?php

namespace Drupal\eresources;

/**
 * Interface for an eResources search result.
 *
 * @package Drupal\eresources
 */
interface ResultInterface {

  /**
   * Result type (eg. local, kb).
   *
   * @return string
   *   Result type.
   */
  public function getResultType();

  /**
   * Title.
   *
   * @return string
   *   Title.
   */
  public function getTitle();

  /**
   * Collection Name.
   *
   * @return string
   *   Collection Name.
   */
  public function getCollectionName();

  /**
   * OCLC Number.
   *
   * @return string
   *   OCLC Number.
   */
  public function getOclcNum();

  /**
   * ISBN.
   *
   * @return string
   *   ISBN.
   */
  public function getIsbn();

  /**
   * ISSN.
   *
   * @return string
   *   ISSN.
   */
  public function getIssn();

  /**
   * EISSN.
   *
   * @return string
   *   eISSN.
   */
  public function getEissn();

  /**
   * Via URL.
   *
   * @return string
   *   Via URL.
   */
  public function getViaUrl();

  /**
   * Author.
   *
   * @return string
   *   Author.
   */
  public function getAuthor();

  /**
   * Publisher.
   *
   * @return string
   *   Publisher.
   */
  public function getPublisher();

  /**
   * Coverage.
   *
   * @return string
   *   Coverage.
   */
  public function getCoverage();

  /**
   * Coverage Enum.
   *
   * @return string
   *   Coverage Enum.
   */
  public function getCoverageEnum();

  /**
   * Coverage notes.
   *
   * @return string
   *   Coverage notes.
   */
  public function getCoverageNotes();

  /**
   * Collection user notes.
   *
   * @return string
   *   Collection user notes.
   */
  public function getCollectionUserNotes();

  /**
   * Location.
   *
   * @return string
   *   Location.
   */
  public function getLocation();

}
