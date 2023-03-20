<?php

namespace Drupal\ior\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'selection' entity_reference.
 *
 * @EntityReferenceSelection(
 *   id = "ior_contest",
 *   label = @Translation("Filter by an IOR contest."),
 *   group = "ior_contest",
 *   entity_types = {
 *     "ior_award",
 *     "ior_collection",
 *   },
 *   weight = 0
 * )
 */
class ContestSelection extends SelectionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Get the entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   An entity type manager.
   */
  protected function entityTypeManager() {
    return $this->entityTypeManager;
  }

  /**
   * Get a storage handler for the target entity type.
   *
   * @return \Drupal\Core\Entity\ContentEntityStorageInterface
   *   An entity storage handler.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function storage() {
    $target_type = $this->getConfiguration()['target_type'];
    /** @var \Drupal\Core\Entity\ContentEntityStorageInterface $storage */
    $storage = $this->entityTypeManager()
      ->getStorage($target_type);
    return $storage;
  }

  /**
   * Create a new ContestSelection plugin instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   An entity type manager.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('entity_type.manager'),
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * Get the contest.
   *
   * @return \Drupal\ior\Entity\ContestInterface
   *   A contest entity.
   */
  protected function getContest() {
    return $this->getSubmission()
      ->getContest();
  }

  /**
   * Get the submission.
   *
   * @return \Drupal\ior\Entity\SubmissionInterface
   *   A submission entity.
   */
  protected function getSubmission() {
    /** @var \Drupal\ior\Entity\SubmissionInterface $submission */
    $submission = $this->getConfiguration()['entity'];
    return $submission;
  }

  /**
   * Build the query.
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   *   A query yielding all entities of the target type that are associated
   *   with the contest of the current submission.
   */
  protected function buildQuery() {
    $contest_id = $this->getContest()->id();
    return $this->storage()
      ->getQuery()
      ->condition('field_contest', $contest_id);
  }

  /**
   * {@inheritDoc}
   */
  public function getReferenceableEntities($match = NULL, $match_operator = 'CONTAINS', $limit = 0) {
    $ids = $this->buildQuery()
      ->execute();

    /** @var \Drupal\Core\Entity\ContentEntityInterface[] $entities */
    $entities = $this->storage()->loadMultiple($ids);

    $options = [];
    foreach ($entities as $entity) {
      $bundle = $this->getBundle($entity);
      $options[$bundle][] = $entity->label();
    }

    return $options;
  }

  /**
   * Get the target entity's bundle.
   *
   * @return string
   *   The given entity's bundle.
   */
  protected function getBundle(ContentEntityInterface $entity) {
    $entity_type = $entity->getEntityType();
    if ($bundle = $entity_type->getKey('bundle')) {
      return $entity->get($bundle)->entity;
    }
    return $entity_type->id();
  }

  /**
   * {@inheritDoc}
   */
  public function countReferenceableEntities($match = NULL, $match_operator = 'CONTAINS') {
    return $this->buildQuery()
      ->count()
      ->execute();
  }

  /**
   * {@inheritDoc}
   */
  public function validateReferenceableEntities(array $ids) {
    return $this->buildQuery()
      ->execute();
  }

}
