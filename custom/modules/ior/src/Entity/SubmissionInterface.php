<?php

namespace Drupal\ior\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Interface for "submission" entities.
 */
interface SubmissionInterface extends ContentEntityInterface, EntityPublishedInterface {

  const FIELD_TITLE = 'field_title';

  /**
   * Get the title.
   *
   * @return string
   *   A string.
   */
  public function getTitle();

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
