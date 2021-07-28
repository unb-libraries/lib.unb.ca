<?php

namespace Drupal\portolan\Field;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;

/**
 * Computed "title" where leading stop-words are removed.
 *
 * @package Drupal\portolan\Field
 */
class TrimmedTitle extends FieldItemList {

  use ComputedItemListTrait;

  /**
   * Computes a sortable title, i.e. one that contains no leading stop words.
   */
  protected function computeValue() {
    $values = [];
    foreach ($this->getEntity()->get('title') as $delta => $item) {
      $computed_title = strtolower($item->value);
      $computed_title = preg_replace('/[.,"\-_\'\/():]/', '', $computed_title);
      $computed_title = $this->getStrippedValue($computed_title);
      $values[$delta] = implode(' ', $computed_title);
    }
    $this->setValue($values);
  }

  /**
   * Get the title without leading stop words.
   *
   * @param string $value
   *   The original string.
   *
   * @return string
   *   A string.
   */
  protected function getStrippedValue($value) {
    $stripped_value = array_filter(explode(' ', $value));
    $stop_words = $this->getStopWords();
    while (in_array($stripped_value[0], $stop_words)) {
      $stripped_value = array_slice($stripped_value, 1);
    }
    return $stripped_value;
  }

  /**
   * Get all words that are considered "stop words" and shall be removed.
   *
   * @return string[]
   *   An array of strings.
   */
  protected function getStopWords() {
    return [
      'a',
      'the',
    ];
  }

}
