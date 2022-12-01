<?php

namespace Drupal\public_profile\Services;

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
    // Use admin theme for profile editing.
    if ($route = $collection->get('profile.user_page.single')) {
      $route->setOption('_admin_route', TRUE);
    }
  }

}
