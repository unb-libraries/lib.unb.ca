<?php

namespace Drupal\simple_group\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Simple group entities.
 *
 * @ingroup simple_group
 */
interface SimpleGroupInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Simple group name.
   *
   * @return string
   *   Name of the Simple group.
   */
  public function getName();

  /**
   * Sets the Simple group name.
   *
   * @param string $name
   *   The Simple group name.
   *
   * @return \Drupal\simple_group\Entity\SimpleGroupInterface
   *   The called Simple group entity.
   */
  public function setName($name);

  /**
   * Gets the Simple group creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Simple group.
   */
  public function getCreatedTime();

  /**
   * Sets the Simple group creation timestamp.
   *
   * @param int $timestamp
   *   The Simple group creation timestamp.
   *
   * @return \Drupal\simple_group\Entity\SimpleGroupInterface
   *   The called Simple group entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Simple group published status indicator.
   *
   * Unpublished Simple group are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Simple group is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Simple group.
   *
   * @param bool $published
   *   TRUE to set this to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\simple_group\Entity\SimpleGroupInterface
   *   The called Simple group entity.
   */
  public function setPublished($published);

}
