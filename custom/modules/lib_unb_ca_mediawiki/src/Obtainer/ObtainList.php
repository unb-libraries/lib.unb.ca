<?php

namespace Drupal\lib_unb_ca_mediawiki\Obtainer;

use Drupal\migration_tools\Obtainer\ObtainArray;
use QueryPath\DOMQuery;

/**
 * Class ObtainList.
 *
 * Contains logic for parsing (ordered or unordered) list(s).
 */
class ObtainList extends ObtainArray {

  /**
   * Find contents from a list and place into an array.
   *
   * @param string $selector
   *   The css selector of the item to search for (the parent item)
   * @param int $list_num
   *   The value of n where the list is the nth list on the page. E.g., 2 for
   *   the second list on a page.
   * @param string $method
   *   What to build array with QP->text() or QP->html().
   *
   * @return array
   *   The table array.
   */
  protected function arrayFindList($selector, $list_num = 1, $method = 'text') {
    return $this->arrayPluckList($selector, $list_num, $method, FALSE);
  }

  /**
   * Pluck contents from a list and place into an array.
   *
   * @param string $selector
   *   The css selector of the item to search for (the parent item)
   * @param int $list_num
   *   The value of n where the list is the nth list on the page. E.g., 2 for
   *   the second list on a page.
   * @param string $method
   *   What to build array with QP->text() or QP->html().
   * @param bool $pluck
   *   (optional) Used internally to declare if the items should be removed.
   *
   * @return array
   *   The table array.
   */
  protected function arrayPluckList($selector, $list_num = 0, $method = 'text', $pluck = TRUE) {
    $html_lists = $this->queryPath->find($selector)->find('ul, ol');
    if ($list_num > count($html_lists)) {
      return [];
    }

    $lists = [];
    foreach ($html_lists as $index => $list) {
      if ($list_num === 0 || $index === $list_num - 1) {
        $items = $this->findListElements($list, $method);
        if ($pluck) {
          $this->setElementToRemove($list);
        }

        if ($list->tag() === 'ol') {
          $ordered = TRUE;
        }
        else {
          $ordered = FALSE;
        }
        $lists[] = [
          'parent' => $list->parent($selector),
          'ordered' => $ordered,
          'items' => $items,
        ];
      }
    }

    return $lists;
  }

  /**
   * Find <li> tags within the given list.
   *
   * @param \QueryPath\DOMQuery $list
   *   The list to parse.
   * @param string $method
   *   What to build array with QP->text() or QP->html().
   *
   * @return array
   *   Array containing HTML <li> tags.
   */
  protected function findListElements(DOMQuery $list, $method) {
    $li_items = $list->find("li");
    $items = [];
    foreach ($li_items as $li) {
      if ($method == '') {
        $items[] = clone $li;
      }
      else {
        $items[] = $li->$method();
      }
    }

    return $items;
  }

}
