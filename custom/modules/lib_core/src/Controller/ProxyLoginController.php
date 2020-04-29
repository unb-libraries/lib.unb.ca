<?php

namespace Drupal\lib_core\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the lib_core module.
 */
class ProxyLoginController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function proxyLoginPage() {
    return [
      '#theme' => 'proxy_login',
      '#url' => \Drupal::request()->query->get('url'),
    ];
  }

}
