<?php

namespace Drupal\node_path_taxonomy\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Node taxonomy path entity.
 *
 * @ingroup node_path_taxonomy
 *
 * @ContentEntityType(
 *   id = "node_taxonomy_path",
 *   label = @Translation("Node taxonomy path"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\node_path_taxonomy\Form\NodeTaxonomyPathForm",
 *       "add" = "Drupal\node_path_taxonomy\Form\NodeTaxonomyPathForm",
 *       "edit" = "Drupal\node_path_taxonomy\Form\NodeTaxonomyPathForm",
 *       "delete" = "Drupal\node_path_taxonomy\Form\NodeTaxonomyPathDeleteForm",
 *     },
 *     "access" = "Drupal\node_path_taxonomy\NodeTaxonomyPathAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\node_path_taxonomy\NodeTaxonomyPathHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "node_taxonomy_path",
 *   admin_permission = "administer node taxonomy path entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/node_taxonomy_path/{node_taxonomy_path}",
 *     "add-form" = "/admin/structure/node_taxonomy_path/add",
 *     "edit-form" = "/admin/structure/node_taxonomy_path/{node_taxonomy_path}/edit",
 *     "delete-form" = "/admin/structure/node_taxonomy_path/{node_taxonomy_path}/delete",
 *     "collection" = "/admin/structure/node_taxonomy_path",
 *   },
 *   field_ui_base_route = "node_taxonomy_path.settings"
 * )
 */
class NodeTaxonomyPath extends ContentEntityBase implements NodeTaxonomyPathInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Node taxonomy path entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE);

    $fields['nid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Article'))
      ->setDescription(t('The associated article.'))
      ->setSetting('target_type', 'node')
      ->setSetting('handler', 'default');

    $fields['tid'] = BaseFieldDefinition::create('entity_reference')
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler', 'default:taxonomy_term');

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Node taxonomy path is published.'))
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

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
  public static function removeAllNodeTypePaths($node_type) {
    $nids = \Drupal::entityQuery('node')
      ->condition('type', $node_type)
      ->execute();
    $nodes = Node::loadMultiple($nids);

    foreach ($nodes as $node) {
      self::removeNodePaths($node);
    }
  }

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
  public static function removeNodePaths(NodeInterface $node) {
    $result = \Drupal::entityQuery('node_taxonomy_path')
      ->condition('nid', $node->id())
      ->execute();

    $storage_handler = \Drupal::entityTypeManager()->getStorage('node_taxonomy_path');
    $entities = $storage_handler->loadMultiple($result);
    $storage_handler->delete($entities);
  }

  /**
   * Add a node path relationship for a specific node.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to create the relationship for.
   * @param int $path_tid
   *   The path taxonomy term ID to associate with the node.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function addNodePath(NodeInterface $node, $path_tid) {
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
  public static function getNodePath(NodeInterface $node) {
    return self::getTermPathValue(
      self::getNodePathTerm($node)
    );
  }

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
  public static function getTermPathValue(TermInterface $term) {
    $paths = [];
    $ancestors = array_reverse(
      \Drupal::service('entity_type.manager')->getStorage("taxonomy_term")->loadAllParents($term->id())
    );
    $vid = $term->bundle();

    foreach ($ancestors as $ancestor) {
      if (!in_array($ancestor->id(), [0, self::getRootTid($vid)])) {
        $paths[] = $ancestor->label();
      }
    }
    return '/' . implode('/', $paths);
  }

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
  protected static function getRootTid($vid = NULL) {
    $properties = [
      'name' => NodeTaxonomyPathRelationship::TAXONOMY_ROOT_ELEMENT,
      'vid' => $vid,
    ];
    $terms = \Drupal::entityManager()->getStorage('taxonomy_term')->loadByProperties($properties);
    $term = reset($terms);

    return !empty($term) ? $term->id() : NULL;
  }

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
  public static function getNodePathTerm(NodeInterface $node) {
    $results = \Drupal::entityQuery('node_taxonomy_path')
      ->condition('nid', $node->id())
      ->execute();
    foreach ($results as $result) {
      if (!empty($result)) {
        $storage_handler = \Drupal::entityTypeManager()->getStorage('node_taxonomy_path');
        $entity = $storage_handler->load($result);
        $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load(
          $entity->get('tid')->target_id
        );
        return $term;
      }
    }
    return NULL;
  }

  /**
   * Get the standardized state key to store/query when processing a node.
   *
   * @return string
   *   The standardized state key ID.
   */
  public static function getStateKey() {
    $user_id = \Drupal::currentUser()->id();
    return "node_taxonomy_path_{$user_id}";
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

}
