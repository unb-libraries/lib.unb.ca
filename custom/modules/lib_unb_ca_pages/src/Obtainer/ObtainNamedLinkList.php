<?php

namespace Drupal\lib_unb_ca_pages\Obtainer;

use Drupal\migration_tools\Obtainer\ObtainLink;

/**
 * Class ObtainNamedLinkList
 *
 * Contains logic for parsing for a list of titled list of links in HTML.
 */
class ObtainNamedLinkList extends ObtainLink {

  /**
   * Find an pluck an <h>-tag along with all links in contents of the selector, put each element in an array.
   *
   * @param string $selector
   *   The selector to find.
   * @param int $n
   *   (optional) The depth to find.  Default: first item n=1.
   * @param bool $pluck
   *   (optional) Used internally to declare if the items should be removed.
   *
   * @return array
   *   The array of elements found, containing title, links
   */
  protected function pluckTitleAndLinks($selector, $n = 1, $pluck = TRUE) {
    $h = $this->pluckListTitle($selector, $n, $pluck);
    $links = $this->pluckLinks($selector, $pluck);

    return [
      'title' => $h,
      'links' => $links,
    ];
  }

  /**
   * Find and pluck any <h*>-tag.
   *
   * @param string $selector
   *   The selector to find.
   * @param int $n
   *   (optional) The depth to find.  Default: first item n=1.
   * @param bool $pluck
   *   (optional) Used internally to declare if the items should be removed.
   *
   * @return array
   *   The text content of the n-th <h*>-tag which was found.
   */
  protected function pluckListTitle($selector, $n = 1, $pluck = TRUE) {
    $h_selector = implode(', ', [
      $selector . ' h1',
      $selector . ' h2',
      $selector . ' h3',
      $selector . ' h4',
      $selector . ' h5',
      $selector . ' h6',
    ]);

    return $this->pluckSelector($h_selector, $n, 'text', $pluck);
  }

}
