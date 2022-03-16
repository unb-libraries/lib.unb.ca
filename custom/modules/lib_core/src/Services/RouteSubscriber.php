<?php

namespace Drupal\lib_core\Services;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Limit profile viewing to admin-like accounts.
    if ($route = $collection->get('entity.user.canonical')) {
      $route->setRequirement('_permission', 'administer users');
    }
  }

}
