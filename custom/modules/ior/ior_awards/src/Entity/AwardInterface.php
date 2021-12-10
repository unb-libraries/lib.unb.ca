<?php

namespace Drupal\ior_awards\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\ior\Entity\SubmissionInterface;

/**
 * Interface for IOR Award entities.
 */
interface AwardInterface extends ContentEntityInterface {

  const FIELD_SUBMISSION = 'field_submission';
  const FIELD_TYPE = 'field_type';

  /**
   * Get the awarded submission.
   *
   * @return \Drupal\ior\Entity\SubmissionInterface
   *   A submission entity.
   */
  public function getSubmission();

  /**
   * Set the awarded submission.
   *
   * @param \Drupal\ior\Entity\SubmissionInterface $submission
   *   A submission entity.
   */
  public function setSubmission(SubmissionInterface $submission);

  /**
   * Get the IOR award type.
   *
   * @return \Drupal\ior_awards\Entity\AwardTypeInterface
   *   An IOW award type entity.
   */
  public function getType();

  /**
   * Get the IOR award type ID.
   *
   * @return string
   *   A string.
   */
  public function getTypeId();

}
