<?php

namespace Drupal\guides\Entity\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\custom_entity\Entity\Routing\HtmlRouteProvider;
use Drupal\custom_entity_revisions\Entity\Routing\RevisionsRouteProviderTrait;

/**
 * Html route builder for "Guide" entities.
 */
class GuideRouteProvider extends HtmlRouteProvider {

  use RevisionsRouteProviderTrait;

  /**
   * {@inheritDoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $routes = parent::getRoutes($entity_type);
    $routes->addCollection($this->getAllRevisionRoutes($entity_type));
    return $routes;
  }

}