<?php

namespace Drupal\ior\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\custom_entity\Entity\UserCreatedInterface;
use Drupal\custom_entity\Entity\UserEditedInterface;

/**
 * Interface for "submission" entities.
 */
interface SubmissionInterface extends ContentEntityInterface, EntityPublishedInterface, UserCreatedInterface, UserEditedInterface {

  const FIELD_FIRST_NAME = 'field_first_name';
  const FIELD_LAST_NAME = 'field_last_name';
  const FIELD_EMAIL = 'field_email';
  const FIELD_DEPARTMENT = 'field_department';
  const FIELD_TITLE = 'field_title';
  const FIELD_DESCRIPTION = 'field_description';

  /**
   * Get the contestant's first name.
   *
   * @return string
   *   A string.
   */
  public function getFirstName();

  /**
   * Get the contestant's last name.
   *
   * @return string
   *   A string.
   */
  public function getLastName();

  /**
   * Get the contestant's email.
   *
   * @return string
   *   An email string.
   */
  public function getEmail();

  /**
   * Get the contestant's faculty.
   *
   * @return string
   *   A string
   */
  public function getDepartment();

  /**
   * Get the submission title.
   *
   * @return string
   *   A string.
   */
  public function getTitle();

  /**
   * The submission abstract.
   *
   * @return string
   *   A (long) string/text.
   */
  public function getDescription();

  /**
   * Get the contest.
   *
   * @return \Drupal\ior\Entity\ContestInterface
   *   A contest entity.
   */
  public function getContest();

  /**
   * Set the contest.
   *
   * @param \Drupal\ior\Entity\ContestInterface $contest
   *   A contest entity.
   */
  public function setContest(ContestInterface $contest);

}
