<?php

namespace Drupal\ior_awards\Entity\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\custom_entity\Entity\Routing\HtmlRouteProvider;
use Drupal\custom_entity_revisions\Entity\Routing\RevisionsRouteProviderTrait;

/**
 * Html route provider for IOR award entities.
 */
class AwardHtmlRouteProvider extends HtmlRouteProvider {

  use RevisionsRouteProviderTrait;

  /**
   * {@inheritDoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $routes = parent::getRoutes($entity_type);

    foreach ($routes as $route) {
      if (!$parameters = $route->getOption('parameters')) {
        $parameters = [];
      }
      $parameters['contest'] = [
        'type' => 'entity:contest',
      ];
      $parameters['ior_submission'] = [
        'type' => 'entity:ior_submission',
      ];
      $route->setOption('parameters', $parameters);
    }

    return $routes;
  }

}
