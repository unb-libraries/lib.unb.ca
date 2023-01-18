<?php

namespace Drupal\ior\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\custom_entity\Entity\UserCreatedInterface;
use Drupal\custom_entity\Entity\UserEditedInterface;
use Drupal\custom_entity_revisions\Entity\RevisionsInterface;

/**
 * Interface for "submission" entities.
 */
interface SubmissionInterface extends ContentEntityInterface, RevisionsInterface, EntityPublishedInterface, UserCreatedInterface, UserEditedInterface {

  const FIELD_FIRST_NAME = 'first_name';
  const FIELD_LAST_NAME = 'last_name';
  const FIELD_EMAIL = 'email';
  const FIELD_DEPARTMENT = 'department';
  const FIELD_TITLE = 'title';
  const FIELD_DESCRIPTION = 'description';
  const FIELD_IMAGE = 'field_image';
  const FIELD_CONTEST = 'contest';

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
   * Get the image URI.
   *
   * @return string
   *   A URL string.
   */
  public function getImageUrl();

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
