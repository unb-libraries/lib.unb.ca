<?php

namespace Drupal\node_path_taxonomy\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an interface for defining Node taxonomy path relationship entities.
 *
 * @ingroup node_path_taxonomy
 */
interface NodeTaxonomyPathRelationshipInterface extends ConfigEntityInterface {

  /**
   * Create a node path taxonomy tree from an associative array.
   *
   * @param string $vid
   *   The vocabulary ID to creat the tree within.
   * @param int[] $terms
   *   The associative array of terms to create.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function createFromArray($vid, array $terms);

  /**
   * Get the root element of a taxonomy path vocabulary.
   *
   * @param string $vid
   *   The vocabulary ID to query.
   *
   * @return int|null
   *   The root element term ID, if it exists. NULL otherwise.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function getRootElement($vid = NULL);

  /**
   * Get the term ID of a vocabulary's term by the name of the term.
   *
   * @param string $name
   *   The name of the term to query.
   * @param string $vid
   *   The vid of the vocabulary to query.
   *
   * @return int|null
   *   The tid of the term if found, NULL otherwise.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getTidByName($name = NULL, $vid = NULL);

  /**
   * Get node types that do not have a configured taxonomy path associated.
   *
   * @return string[]
   *   An array of node types that do not have a configured taxonomy path.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getUnConfiguredNodeTypes();

  /**
   * Get all created node types.
   *
   * @return string[]
   *   An array of node types that are currently defined.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getNodeTypes();

  /**
   * Get the node types that have a configured taxonomy path.
   *
   * @return string[]
   *   An array of node types that have a configured taxonomy path.
   */
  public static function getConfiguredNodeTypes();

  /**
   * Determine if a relationship is set for an entity base on its form state.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state to query.
   *
   * @return bool
   *   TRUE if a relationship has been set, FALSE otherwise.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getRelationshipSetFromFormState(FormStateInterface $form_state);

  /**
   * Get all paths that have been set for a node type.
   *
   * @param string $node_type
   *   The node type to query.
   *
   * @return string[]
   *   An associative array of paths for the node type, keyed by the Term ID.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getPaths($node_type);

  /**
   * Get the vocabulary ID of the associated node type relationship.
   *
   * @param string $node_type
   *   The node type to query.
   *
   * @return string
   *   The VID of the vocabulary associated with the node type.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function loadByNodeType($node_type);

  /**
   * Get the node type associated with this relationship.
   *
   * @return string
   *   The node type associated with this relationship.
   */
  public function getNodeType();

  /**
   * Get the vocabulary ID associated with this relationship.
   *
   * @return string
   *   The vocabulary ID associated with this relationship.
   */
  public function getVid();

}
