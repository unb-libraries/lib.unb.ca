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
      if (!$parameters = $route->getOption('parameters')) {
        $parameters = [];
      }
      $parameters['contest'] = [
        'type' => 'entity:contest',
      ];
      $route->setOption('parameters', $parameters);
    }

    return $routes;
  }

  /**
   * {@inheritDoc}
   */
  protected function getAddFormRoute(EntityTypeInterface $entity_type) {
    if ($route = parent::getAddFormRoute($entity_type)) {
      $route->setDefault('_controller', 'submission.form_controller:addForm');
      $route->setOption('no_cache', TRUE);
      return $route;
    }
    return FALSE;
  }

}
