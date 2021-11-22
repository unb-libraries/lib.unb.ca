<?php

namespace Drupal\ior\Entity\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Route subscriber altering entity.ior_submission routes.
 */
class SubmissionRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritDoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    foreach ($collection->all() as $route) {
      $parameters = $route->getOption('parameters');
      if (is_array($parameters) && array_key_exists('ior_submission', $parameters)) {
        $parameters['ior_submission']['type'] = 'entity:contest:ior_submission';
        if (!array_key_exists('contest', $parameters)) {
          $parameters['contest'] = [
            'type' => 'entity:contest',
          ];
        }
        $route->setOption('parameters', $parameters);
      }
    }
  }

}
