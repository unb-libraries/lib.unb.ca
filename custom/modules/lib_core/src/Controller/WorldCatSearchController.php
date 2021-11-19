<?php

namespace Drupal\lib_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Routing\TrustedRedirectResponse;

/**
 * Provides route responses for the lib_core module.
 */
class WorldCatSearchController extends ControllerBase {

  /**
   * Parses input from a custom form and redirects the user to WorldCat.
   */
  public function worldCatSearchHelper(Request $request) {
    if (empty($request->request->get('queryString'))) {
      $response = new RedirectResponse('/');
      $response->send();
      return;
    }

    $keys = ['queryString', 'scope', 'format', 'databaseList'];
    $index = str_replace("&quot;", '"', stripslashes($request->request->get('searchIndex')));
    $q = [];

    foreach ($keys as $key) {
      $value = $request->request->get($key);
      if (!empty($value) || $key == 'scope') {
        if ($key == 'format' && strpos($value, '::') !== FALSE) {
          $key = 'subformat';
        }
        $q[$key] = str_replace("&quot;", '"', stripslashes($value));

        if ($key == 'queryString' && $index != 'kw') {
          if ($index == 'tj') {
            $q[$key] = "ti:{$q[$key]} AND mt:cnr";
          }
          else {
            $q[$key] = "{$index}:{$q[$key]}";
          }
        }
      }
    }

    // QUERY SYNTAX
    // https://unb.on.worldcat.org/v2/search?scope=wz%3A66413&queryString=monkeys
    $url = "https://unb.on.worldcat.org/v2/search?" . http_build_query($q);
    return new TrustedRedirectResponse($url);
  }

}
