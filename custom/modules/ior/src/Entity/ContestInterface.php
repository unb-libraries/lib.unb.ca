<?php

namespace Drupal\ior\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Interface for "contest" entities.
 */
interface ContestInterface extends ContentEntityInterface {

  const FIELD_TITLE = 'field_title';
  const FIELD_DESCRIPTION = 'field_description';
  const FIELD_DATE_OPEN = 'field_date_open';
  const FIELD_DATE_CLOSE = 'field_date_close';

  /**
   * Get the contest title.
   *
   * @return string
   *   A string.
   */
  public function getTitle();

  /**
   * Get the contest description.
   *
   * @return string
   *   A string.
   */
  public function getDescription();

  /**
   * Get the date the contest opens.
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime
   *   A Drupal datetime object.
   */
  public function getOpenDate();

  /**
   * Get the date the contest closes.
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime
   *   A Drupal datetime object.
   */
  public function getCloseDate();

  /**
   * Whether the contest is currently open.
   *
   * @return bool
   *   TRUE if the current date is between the open and close date.
   *   FALSE otherwise.
   */
  public function isOpen();

  /**
   * Whether the contest has not yet opened.
   *
   * @return bool
   *   TRUE if the current date is before the open date. FALSE otherwise.
   */
  public function isComingUp();

  /**
   * Whether the contest has closed.
   *
   * @return bool
   *   TRUE if the current date is past the close date. FALSE otherwise.
   */
  public function isClosed();

  /**
   * Get all submissions this contest has received.
   *
   * @return \Drupal\ior\Entity\SubmissionInterface[]
   *   An array of submission entities.
   */
  public function getSubmissions();

}
