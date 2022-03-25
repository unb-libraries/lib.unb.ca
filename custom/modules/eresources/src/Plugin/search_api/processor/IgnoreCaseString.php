<?php

namespace Drupal\eresources\Plugin\search_api\processor;

use Drupal\search_api\Plugin\search_api\processor\IgnoreCase;

/**
 * Allow case insensitive processing on strings.
 *
 * @SearchApiProcessor(
 *   id = "ignorecase_string",
 *   label = @Translation("Ignore Case (String)"),
 *   description = @Translation("Allow case insensitive processing on strings."),
 *   stages = {
 *     "pre_index_save" = 0,
 *     "preprocess_index" = -20,
 *     "preprocess_query" = -20
 *   }
 * )
 */
class IgnoreCaseString extends IgnoreCase {

  /**
   * {@inheritdoc}
   */
  protected function testType($type) {
    return $this->getDataTypeHelper()
      ->isTextType($type, ['string']);
  }

}
