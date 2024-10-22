<?php

namespace Drupal\lib_unb_ca_mediawiki\Obtainer;

use Drupal\migration_tools\Obtainer\ObtainLink;

/**
 * Class ObtainNamedLinkList.
 *
 * Contains logic for parsing for a list of titled list of links in HTML.
 */
class ObtainNamedLinkList extends ObtainList {

  /**
   * Find links contained in an ordered or unordered list and put each into an array.
   *
   * @param string $selector
   *   The selector to find.
   *
   * @return array
   *   The array of elements found containing ordered, links
   */
  protected function arrayFindLinkLists($selector) {
    return $this->arrayPluckLinkLists($selector, FALSE);
  }

  /**
   * Find and pluck links contained in an ordered or unordered list and put each into an array.
   *
   * @param string $selector
   *   The selector to find.
   * @param bool $pluck
   *   (optional) Whether to pluck the found content.
   *
   * @return array
   *   The array of elements found containing ordered, links
   */
  protected function arrayPluckLinkLists($selector, $pluck = TRUE) {
    $lists = $this->arrayFindList($selector, 0, '');

    $link_lists = [];
    foreach ($lists as $list) {
      $list_title = $list['parent']->find('h1, h2, h3, h4, h5, h6')->get(0)->textContent;

      $links = [];
      foreach ($list['items'] as $item) {
        $url = $item->find('a')->get(0)->getAttribute('href');
        $title = $item->find('a')->get(0)->textContent;
        $links[] = [
          'href' => $url,
          'link_text' => $title,
        ];
      }

      $link_lists[] = [
        'title' => $list_title,
        'ordered' => $list['ordered'],
        'links' => $links,
      ];
    }

    if ($pluck) {
      $this->setElementToRemove($this->queryPath->find($selector));
    }

    return $link_lists;
  }

}
