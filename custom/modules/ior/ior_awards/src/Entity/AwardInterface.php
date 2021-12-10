<?php

namespace Drupal\ior_awards\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\ior\Entity\SubmissionInterface;

/**
 * Interface for IOR Award entities.
 */
interface AwardInterface extends ContentEntityInterface {

  const FIELD_SUBMISSION = 'field_submission';

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

}
