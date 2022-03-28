<?php

namespace Drupal\eresources\Plugin\search_api\processor;

use Drupal\search_api\Processor\FieldsProcessorPluginBase;

/**
 * Special processing for "starts with" searches.
 *
 * @SearchApiProcessor(
 *   id = "startswith",
 *   label = @Translation("'Starts With' Processing"),
 *   description = @Translation("Special processing for 'starts with' searches."),
 *   stages = {
 *     "pre_index_save" = 0,
 *     "preprocess_index" = -20,
 *     "preprocess_query" = -20
 *   }
 * )
 */
class StartsWith extends FieldsProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  protected function testType($type) {
    return $this->getDataTypeHelper()
      ->isTextType($type, ['string']);
  }

  /**
   * {@inheritdoc}
   */
  protected function process(&$value) {
    $value = mb_strtolower($value);
    $value = preg_replace('/\s+/', '-', $value);
  }

}
