<?php

namespace Drupal\node_path_taxonomy\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
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
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
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
   * {@inheritdoc}
   */
  public static function addNodePathRelationship(NodeInterface $node, $path_tid) {
    $data = [
      'uid' => \Drupal::currentUser()->id(),
      'nid' => $node->id(),
      'tid' => $path_tid,
    ];
    $node_taxonomy_path = \Drupal::entityManager()
      ->getStorage('node_taxonomy_path')
      ->create($data);
    $node_taxonomy_path->save();
  }

  /**
   * {@inheritdoc}
   */
  public static function setNodePathRelationship(NodeInterface $node, $path_tid) {
    NodeTaxonomyPath::removeNodePaths($node);
    self::addNodePathRelationship($node, $path_tid);
  }

  /**
   * {@inheritdoc}
   */
  public static function addNodePathRelationshipFromPath(NodeInterface $node, $vid, $path) {
    $path_term = NodeTaxonomyPath::getPathTermFromPath($vid, $path);
    if (!empty($path_term)) {
      self::addNodePathRelationship($node, $path_term->id());
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function createFromArray($vid, array $terms) {
    $root_tid = self::getRootElement($vid);
    self::createPathElements($vid, $root_tid, $terms);
  }

  /**
   * {@inheritdoc}
   */
  private static function createPathElements($vid, $parent_tid, $values) {
    if (is_array($values)) {
      foreach ($values as $element => $sub_elements) {
        $parent = self::createElement($parent_tid, $element, $vid);
        if (!empty($sub_elements)) {
          self::createPathElements($vid, $parent->id(), $sub_elements);
        }
      }
    }
    else {
      self::createElement($parent_tid, $values, $vid);
    }
  }

  /**
   * {@inheritdoc}
   */
  private static function createElement($parent_tid, $name, $vid) {
    $term = Term::create(
      [
        'parent' => [$parent_tid],
        'name' => $name,
        'vid' => $vid,
      ]
    );
    $term->save();
    return $term;
  }

  /**
   * {@inheritdoc}
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
   * {@inheritdoc}
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
   * {@inheritdoc}
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
   * {@inheritdoc}
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
  public static function getConfiguredNodeTypes() {
    $configured_types = [];
    $results = \Drupal::entityQuery('node_taxonomy_path_relationship')
      ->execute();
    foreach ($results as $result) {
      $configured_types[] = str_replace(NODE_PATH_TAXONOMY_RELATIONSHIP_CONFIG_ID_PREFIX, '', $result);
    }
    return $configured_types;
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
   * {@inheritdoc}
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

  /**
   * {@inheritdoc}
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
   * {@inheritdoc}
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
   * {@inheritdoc}
   */
  public function getNodeType() {
    return $this->node_type;
  }

  /**
   * {@inheritdoc}
   */
  public function getVid() {
    return $this->vid;
  }

}
