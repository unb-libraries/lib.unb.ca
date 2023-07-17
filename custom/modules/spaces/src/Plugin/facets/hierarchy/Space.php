<?php

namespace Drupal\spaces\Plugin\facets\hierarchy;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\facets\Hierarchy\HierarchyPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin for hierarchical "Space" entity facets.
 *
 * @FacetsHierarchy(
 *   id = "space",
 *   label = @Translation("Space hierarchy"),
 *   description = @Translation("Hierarchy structure provided by the spaces module.")
 * )
 */
class Space extends HierarchyPluginBase {

  /**
   * The space entity storage handler.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageInterface
   */
  protected $spaceStorage;

  /**
   * Get the space entity storage handler.
   *
   * @return \Drupal\Core\Entity\ContentEntityStorageInterface
   *   An entity storage handler for "space" entities.
   */
  protected function spaceStorage() {
    return $this->spaceStorage;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $space_storage = $container->get('entity_type.manager')
      ->getStorage('space');
    return new static($configuration, $plugin_id, $plugin_definition, $space_storage);
  }

  /**
   * Constructs a Drupal\spaces\Plugin\facets\hierarchy\Space object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\ContentEntityStorageInterface $space_storage
   *   The space entity storage handler.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ContentEntityStorageInterface $space_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->spaceStorage = $space_storage;
  }

  /**
   * {@inheritDoc}
   */
  public function getParentIds($id) {
    /** @var \Drupal\spaces\Entity\SpaceInterface $space */
    $space = $this->spaceStorage()->load($id);
    if ($parent = $space->getParent()) {
      return [$parent->id()];
    }
    return [];
  }

  /**
   * {@inheritDoc}
   */
  public function getNestedChildIds($id) {
    $child_ids = array_merge(...$this->getChildIds([$id]));
    return array_merge($child_ids, ...array_map(function (int $id) {
      return $this->getNestedChildIds($id);
    }, $child_ids));
  }

  /**
   * {@inheritDoc}
   */
  public function getChildIds(array $ids) {
    $ids = array_combine(array_values($ids), array_values(($ids)));
    return array_map(function ($id) {
      return array_keys($this->spaceStorage()
        ->loadByProperties(['parent' => $id]));
    }, $ids);
  }

}
