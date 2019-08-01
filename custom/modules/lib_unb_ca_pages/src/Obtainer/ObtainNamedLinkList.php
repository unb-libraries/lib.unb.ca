<?php

namespace Drupal\lib_unb_ca_pages\Obtainer;

use Drupal\migration_tools\Obtainer\ObtainLink;

/**
 * Class ObtainNamedLinkList.
 *
 * Contains logic for parsing for a list of titled list of links in HTML.
 */
class ObtainNamedLinkList extends ObtainLink {

  /**
   * Find an <h>-tag along with all links in contents of the selector, put each element in an array.
   *
   * @param string $selector
   *   The selector to find.
   * @param bool $ordered
   *   Whether to search in an ordered or unordered list.
   * @param int $n
   *   (optional) The depth to find.  Default: first item n=1.
   *
   * @return array
   *   The array of elements found, containing title, links
   */
  protected function findTitleAndLinks($selector, $ordered = FALSE, $n = 1) {
    $list = $this->findLinkList($selector, $ordered);
    if (!empty($list['links'])) {
      $h = $this->findListTitle($selector, $n);
      return [
        'title' => $h,
        'list' => $list,
      ];
    }
    return [];
  }

  /**
   * Find and pluck an <h>-tag along with all links in contents of the selector, put each element in an array.
   *
   * @param string $selector
   *   The selector to find.
   * @param bool $ordered
   *   Whether to search in an ordered or unordered list.
   * @param int $n
   *   (optional) The depth to find.  Default: first item n=1.
   *
   * @return array
   *   The array of elements found, containing title, links
   */
  protected function pluckTitleAndLinks($selector, $ordered = FALSE, $n = 1) {
    $list = $this->pluckLinkList($selector, $ordered);
    if (!empty($list['links'])) {
      $h = $this->pluckListTitle($selector, $n);
      return [
        'title' => $h,
        'list' => $list,
      ];
    }
    return [];
  }

  /**
   * Find any <h*>-tag.
   *
   * @param string $selector
   *   The selector to find.
   * @param int $n
   *   (optional) The depth to find.  Default: first item n=1.
   *
   * @return array
   *   The text content of the n-th <h*>-tag which was found.
   */
  protected function findListTitle($selector, $n = 1) {
    $h_selector = implode(', ', [
      $selector . ' h1',
      $selector . ' h2',
      $selector . ' h3',
      $selector . ' h4',
      $selector . ' h5',
      $selector . ' h6',
    ]);

    return $this->findSelector($h_selector, $n, 'text');
  }

  /**
   * Find and pluck any <h*>-tag.
   *
   * @param string $selector
   *   The selector to find.
   * @param int $n
   *   (optional) The depth to find.  Default: first item n=1.
   *
   * @return array
   *   The text content of the n-th <h*>-tag which was found.
   */
  protected function pluckListTitle($selector, $n = 1) {
    $h_selector = implode(', ', [
      $selector . ' h1',
      $selector . ' h2',
      $selector . ' h3',
      $selector . ' h4',
      $selector . ' h5',
      $selector . ' h6',
    ]);

    return $this->pluckSelector($h_selector, $n, 'text');
  }

  /**
   * Find links contained in an ordered or unordered list and put each into an array.
   *
   * @param string $selector
   *   The selector to find.
   * @param bool $ordered
   *   Whether to search for an ordered or unordered list of links.
   *
   * @return array
   *   The array of elements found containing ordered, links
   */
  protected function findLinkList($selector, $ordered) {
    $list = [];
    if ($ordered) {
      $list['ordered'] = TRUE;
      $list['links'] = $this->findOrderedLinkList($selector);
    }
    else {
      $list['ordered'] = FALSE;
      $list['links'] = $this->findUnorderedLinkList($selector);
    }
    return $list;
  }

  /**
   * Find and pluck links contained in an ordered or unordered list and put each into an array.
   *
   * @param string $selector
   *   The selector to find.
   * @param bool $ordered
   *   Whether to search for an ordered or unordered list of links.
   *
   * @return array
   *   The array of elements found containing ordered, links
   */
  protected function pluckLinkList($selector, $ordered) {
    $list = [];
    if ($ordered) {
      $list['ordered'] = TRUE;
      $list['links'] = $this->pluckOrderedLinkList($selector);
    }
    else {
      $list['ordered'] = FALSE;
      $list['links'] = $this->pluckUnorderedLinkList($selector);
    }
    return $list;
  }

  /**
   * Find links contained in an ordered list and put each into an array.
   *
   * @param string $selector
   *   The selector to find.
   *
   * @return array
   *   The array of elements found containing element, href, link_text, base_uri
   */
  protected function findOrderedLinkList($selector) {
    return $this->findLinks($selector . ' ol li');
  }

  /**
   * Find and pluck links contained in an ordered list and put each into an array.
   *
   * @param string $selector
   *   The selector to find.
   *
   * @return array
   *   The array of elements found containing element, href, link_text, base_uri
   */
  protected function pluckOrderedLinkList($selector) {
    return $this->pluckLinks($selector . ' ol li');
  }

  /**
   * Find links contained in an unordered list and put each into an array.
   *
   * @param string $selector
   *   The selector to find.
   *
   * @return array
   *   The array of elements found containing element, href, link_text, base_uri
   */
  protected function findUnorderedLinkList($selector) {
    return $this->findLinks($selector . ' ul li');
  }

  /**
   * Find and pluck links contained in an unordered list and put each into an array.
   *
   * @param string $selector
   *   The selector to find.
   *
   * @return array
   *   The array of elements found containing element, href, link_text, base_uri
   */
  protected function pluckUnorderedLinkList($selector) {
    return $this->pluckLinks($selector . ' ul li');
  }

}
