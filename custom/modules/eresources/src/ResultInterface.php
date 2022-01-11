<?php

namespace Drupal\eresources;

/**
 * Interface for an eResources search result.
 *
 * @package Drupal\eresources
 */
interface ResultInterface {

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
  public function getOcn();

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
   * Coverage Statement.
   *
   * @return string
   *   Coverage Statement.
   */
  public function getCoverageStatement();

  /**
   * Permitted Use.
   *
   * @return string
   *   Permitted Use.
   */
  public function getPermittedUseStatement();

}
