<?php

namespace Drupal\node_path_taxonomy\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines the Node taxonomy path relationship entity.
 *
 * @ConfigEntityType(
 *   id = "node_taxonomy_path_relationship",
 *   label = @Translation("Node taxonomy path relationship"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\node_path_taxonomy\NodeTaxonomyPathRelationshipListBuilder",
 *     "form" = {
 *       "add" = "Drupal\node_path_taxonomy\Form\NodeTaxonomyPathRelationshipForm",
 *       "edit" = "Drupal\node_path_taxonomy\Form\NodeTaxonomyPathRelationshipForm",
 *       "delete" = "Drupal\node_path_taxonomy\Form\NodeTaxonomyPathRelationshipDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\node_path_taxonomy\NodeTaxonomyPathRelationshipHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "node_taxonomy_path_relationship",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "node_type",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/node_path_taxonomy/node_taxonomy_path_relationship/{node_taxonomy_path_relationship}",
 *     "add-form" = "/admin/config/node_path_taxonomy/node_taxonomy_path_relationship/add",
 *     "delete-form" = "/admin/config/node_path_taxonomy/node_taxonomy_path_relationship/{node_taxonomy_path_relationship}/delete",
 *     "collection" = "/admin/config/node_path_taxonomy/node_taxonomy_path_relationship"
 *   }
 * )
 */
class NodeTaxonomyPathRelationship extends ConfigEntityBase implements NodeTaxonomyPathRelationshipInterface {

  const TAXONOMY_ROOT_ELEMENT = '--root--';

  /**
   * The type.
   *
   * @var string
   */
  protected $id;

  /**
   * The type.
   *
   * @var string
   */
  protected $node_type;

  /**
   * The type.
   *
   * @var string
   */
  protected $vid;

  /**
   * Get the node type associated with this relationship.
   *
   * @return string
   *   The node type associated with this relationship.
   */
  public function getNodeType() {
    return $this->node_type;
  }

  /**
   * Get the vocabulary ID associated with this relationship.
   *
   * @return string
   *   The vocabulary ID associated with this relationship.
   */
  public function getVid() {
    return $this->vid;
  }

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
  public static function loadByNodeType($node_type) {
    $results = \Drupal::entityQuery('node_taxonomy_path_relationship')
      ->condition('node_type', $node_type)
      ->execute();
    foreach ($results as $result) {
      $config_entity = \Drupal::entityTypeManager()
        ->getStorage('node_taxonomy_path_relationship')
        ->load($result);
      return $config_entity->get('vid');
    }
    return NULL;
  }

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
  public static function createFromArray($vid, array $terms) {
    $root_tid = self::getRootElement($vid);

    foreach ($terms as $parent_path => $sub_paths) {
      $parent_term = Term::create(
        [
          'parent' => [$root_tid],
          'name' => $parent_path,
          'vid' => $vid,
        ]
      );
      $parent_term->save();
      if (!empty($sub_paths)) {
        foreach ($sub_paths as $sub_path) {
          Term::create(
            [
              'parent' => [$parent_term->id()],
              'name' => $sub_path,
              'vid' => $vid,
            ]
          )->save();
        }
      }
      unset($parent_term);
    }
  }

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
  public static function getRootElement($vid = NULL) {
    $cur_root_tid = self::getTidByName(self::TAXONOMY_ROOT_ELEMENT, $vid);
    if ($cur_root_tid > 0) {
      return $cur_root_tid;
    }
    $root_term = Term::create(
      [
        'parent' => [],
        'name' => self::TAXONOMY_ROOT_ELEMENT,
        'vid' => $vid,
      ]
    );
    $root_term->save();
    return $root_term->id();
  }

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
  public static function getTidByName($name = NULL, $vid = NULL) {
    $properties = [];
    if (!empty($name)) {
      $properties['name'] = $name;
    }
    if (!empty($vid)) {
      $properties['vid'] = $vid;
    }
    $terms = \Drupal::entityManager()->getStorage('taxonomy_term')->loadByProperties($properties);
    $term = reset($terms);
    return !empty($term) ? $term->id() : 0;
  }

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
  public static function getPaths($node_type) {
    $node_path_taxonomy = NodeTaxonomyPathRelationship::loadByNodeType(($node_type));
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($node_path_taxonomy, 0, NULL, TRUE);
    $options = [];

    foreach ($terms as $term) {
      if ($term->label() == self::TAXONOMY_ROOT_ELEMENT) {
        $options[$term->id()] = '/';
      }
      else {
        $path_value = NodeTaxonomyPath::getTermPathValue($term);
        $options[$term->id()] = $path_value;
      }
    }
    \Drupal::moduleHandler()->invokeAll('node_path_taxonomy_alter_paths', [$node_type, $options]);

    return $options;
  }

  /**
   * Get the node types that have a configured taxonomy path.
   *
   * @return string[]
   *   An array of node types that have a configured taxonomy path.
   */
  public static function getConfiguredNodeTypes() {
    $configured_types = [];
    $results = \Drupal::entityQuery('node_taxonomy_path_relationship')
      ->execute();
    foreach ($results as $result) {
      $configured_types[] = str_replace(NODE_PATH_TAXONOMY_RELATIONSHIP_ID_PREFIX, '', $result);
    }
    return $configured_types;
  }

  /**
   * Get node types that do not have a configured taxonomy path associated.
   *
   * @return string[]
   *   An array of node types that do not have a configured taxonomy path.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getUnConfiguredNodeTypes() {
    $types = self::getNodeTypes();
    $configured_types = self::getConfiguredNodeTypes();
    foreach ($configured_types as $configured_type) {
      if (isset($types[$configured_type])) {
        unset($types[$configured_type]);
      }
    }
    return $types;
  }

  /**
   * Get all created node types.
   *
   * @return string[]
   *   An array of node types that are currently defined.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getNodeTypes() {
    $type_options = [];
    $types = \Drupal::entityTypeManager()
      ->getStorage('node_type')
      ->loadMultiple();

    foreach ($types as $machine_name => $type) {
      $type_options[$machine_name] = $type->label();
    }
    return $type_options;
  }

  /**
   * {@inheritdoc}
   */
  public static function preDelete(EntityStorageInterface $storage, array $entities) {
    parent::preDelete($storage, $entities);
    $delete_types = [];
    foreach ($entities as $entity) {
      $delete_types[] = $entity->getNodeType();
    }
    foreach ($delete_types as $delete_type) {
      NodeTaxonomyPath::removeAllNodeTypePaths($delete_type);
    }
  }

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
  public static function getRelationshipSetFromFormState(FormStateInterface $form_state) {
    if (!empty($form_state->getFormObject()) && method_exists($form_state->getFormObject(), 'getEntity')) {
      $node = $form_state->getFormObject()->getEntity();
      $node_type = $node->bundle();
      $paths = self::getPaths($node_type);
      if (!empty($paths)) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
