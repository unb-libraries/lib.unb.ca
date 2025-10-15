<?php

namespace Drupal\lib_core\Services;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

/**
 * Negotiates the theme based on a custom route option.
 */
class ThemeNegotiator implements ThemeNegotiatorInterface {

 /**
  * {@inheritdoc}
  */
 public function applies(RouteMatchInterface $route_match) {
   $route = $route_match->getRouteObject();
   return $route && $route->hasOption('_custom_theme');
 }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    $route = $route_match->getRouteObject();
    return $route->getOption('_custom_theme');
  }

}
