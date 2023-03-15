<?php

namespace Drupal\lib_core\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the lib_core module.
 */
class ProxyController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function noRedirect() {
    $blockManager = \Drupal::service('plugin.manager.block');
    $askUsBlock = $blockManager->createInstance('askus_popup', []);
    $access = $askUsBlock->access(\Drupal::currentUser());
    if (is_object($access) && $access->isForbidden() || is_bool($access) && !$access) {
      $askUs = '';
    }
    else {
      $renderArray = $askUsBlock->build();
      $askUs = \Drupal::service('renderer')->render($renderArray);
    }

    return [
      '#theme' => 'proxy_noredirect',
      '#url' => \Drupal::request()->query->get('url'),
      '#askus' => $askUs,
    ];
  }

}
