<?php

namespace Drupal\ior\Entity\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\custom_entity\Entity\Routing\HtmlRouteProvider;
use Drupal\custom_entity_revisions\Entity\Routing\RevisionsRouteProviderTrait;

/**
 * Html route provider for "submission" entities.
 */
class SubmissionHtmlRouteProvider extends HtmlRouteProvider {

  use RevisionsRouteProviderTrait;

  /**
   * {@inheritDoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $routes = parent::getRoutes($entity_type);
    $routes->addCollection($this->getAllRevisionRoutes($entity_type));

    foreach ($routes as $route) {
      $parameters = $route->getOption('parameters');
      $parameters['contest'] = [
        'type' => 'entity:contest',
      ];
      $route->setOption('parameters', $parameters);
    }

    return $routes;
  }

}