<?php

namespace Drupal\ior_awards\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\ior\Entity\ContestInterface;

/**
 * Interface for IOR Award entities.
 */
interface AwardInterface extends ContentEntityInterface {

  const FIELD_CONTEST = 'field_contest';

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
