<?php

namespace Drupal\ior\Entity\Storage;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\ior\Entity\SubmissionInterface;
use Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Entity storage handler for ior_submission entities.
 */
class SubmissionStorage extends SqlContentEntityStorage implements SubmissionStorageInterface {

  use RevisionableEntityStorageTrait;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Set the current route match.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   A route match.
   */
  protected function setRouteMatch(RouteMatchInterface $route_match) {
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritDoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    $instance = parent::createInstance($container, $entity_type);
    $instance->setRouteMatch($container->get('current_route_match'));
    return $instance;
  }

  /**
   * {@inheritDoc}
   */
  protected function doCreate(array $values) {
    /** @var \Drupal\ior\Entity\ContestInterface $contest */
    $contest = \Drupal::routeMatch()->getParameter('contest');
    $values['type'] = $contest->getSubmissionType()->id();
    return parent::doCreate($values);
  }

  /**
   * {@inheritDoc}
   */
  public function loadByContest($contest_id, array $options = []) {
    $options = array_merge([
      'published' => NULL,
    ], $options);

    $query = $this->getQuery()
      ->condition(SubmissionInterface::FIELD_CONTEST, $contest_id);

    if (!is_null($options['published'])) {
      $query->condition('published', $options['published']);
    }

    return $this->loadMultiple($query->execute());
  }

}
