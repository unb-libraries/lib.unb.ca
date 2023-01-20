<?php

namespace Drupal\guides\Entity\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\custom_entity\Entity\Routing\HtmlRouteProvider;
use Drupal\custom_entity_revisions\Entity\Routing\RevisionsRouteProviderTrait;

/**
 * Html route builder for "guide_category" entities.
 */
class GuideCategoryRouteProvider extends HtmlRouteProvider {

  use RevisionsRouteProviderTrait;

  /**
   * {@inheritDoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $routes = parent::getRoutes($entity_type);
    $routes->addCollection($this->getAllRevisionRoutes($entity_type));

    $revRoutes = ['revisions', 'revision', 'revision_restore_form'];
    foreach ($revRoutes as $revRoute) {
      $route = $routes->get("entity.guide_category.{$revRoute}");
      $route->setRequirements(['_entity_access' => 'guide_category.update']);
    }

    return $routes;
  }

}
