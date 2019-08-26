<?php

namespace Drupal\node_path_taxonomy\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;
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

  /**
   * Remove all existing node paths relationships for a node type.
   *
   * @param string $node_type
   *   The node type to remove paths for.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function removeAllNodeTypePaths($node_type);

  /**
   * Remove the existing node paths relationships for a node.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to remove paths for.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function removeNodePaths(NodeInterface $node);

  /**
   * Get the base taxonomy path for a node.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to get the base path taxonomy string for.
   *
   * @return string|null
   *   The base taxonomy path if it is set, NULL otherwise.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getNodePath(NodeInterface $node);

  /**
   * Get the base path value for a node path taxonomy term.
   *
   * @param \Drupal\taxonomy\TermInterface $term
   *   The term to determine the base path value for.
   *
   * @return string
   *   The base path value, if it exists.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getTermPathValue(TermInterface $term);

  /**
   * Get the standardized root TID for a specific taxonomy path vocabulary.
   *
   * @param string $vid
   *   The VID to get the root TID for.
   *
   * @return int|null
   *   The TID of the path vocabulary, NULL otherwise.DDDD
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getRootTid($vid = NULL);

  /**
   * Get the path taxonomy term for a specific node.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to get the path taxonomy term for.
   *
   * @return \Drupal\taxonomy\TermInterface|null
   *   The taxonomy term, if it is set. NULL otherwise.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getNodePathTerm(NodeInterface $node);

  /**
   * Get the standardized state key to store/query when processing a node.
   *
   * @return string
   *   The standardized state key ID.
   */
  public static function getPathTaxonomyTidStateKey();

}
