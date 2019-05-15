<?php

namespace Drupal\node_path_taxonomy\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Node taxonomy path entities.
 *
 * @ingroup node_path_taxonomy
 */
interface NodeTaxonomyPathInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Node taxonomy path creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Node taxonomy path.
   */
  public function getCreatedTime();

  /**
   * Sets the Node taxonomy path creation timestamp.
   *
   * @param int $timestamp
   *   The Node taxonomy path creation timestamp.
   *
   * @return \Drupal\node_path_taxonomy\Entity\NodeTaxonomyPathInterface
   *   The called Node taxonomy path entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Node taxonomy path published status indicator.
   *
   * Unpublished Node taxonomy path are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Node taxonomy path is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Node taxonomy path.
   *
   * @param bool $published
   *   TRUE to set this to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\node_path_taxonomy\Entity\NodeTaxonomyPathInterface
   *   The called Node taxonomy path entity.
   */
  public function setPublished($published);

}
