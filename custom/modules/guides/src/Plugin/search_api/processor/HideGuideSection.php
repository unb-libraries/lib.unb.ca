<?php

namespace Drupal\guides\Plugin\search_api\processor;

use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;

/**
 * Excludes hidden guide sections from the search index.
 *
 * @SearchApiProcessor(
 *   id = "guide_hide_section",
 *   label = @Translation("Hide guide sections"),
 *   description = @Translation("Removes indexed field values that originate from guide sections with 'Hide Section' enabled."),
 *   stages = {
 *     "preprocess_index" = 0,
 *   }
 * )
 */
class HideGuideSection extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  public static function supportsIndex(IndexInterface $index) {
    return $index->isValidDatasource('entity:guide');
  }

  /**
   * {@inheritdoc}
   */
  public function preprocessIndexItems(array $items) {
    foreach ($items as $item) {
      $this->filterHiddenSections($item);
    }
  }

  /**
   * Removes field values sourced from the given item's hidden sections.
   *
   * @param \Drupal\search_api\Item\ItemInterface $item
   *   The search index item to filter.
   */
  protected function filterHiddenSections(ItemInterface $item) {
    if ($item->getDatasourceId() !== 'entity:guide') {
      return;
    }

    $guide = $item->getOriginalObject()->getValue();
    if (!$guide || !$guide->hasField('sections')) {
      return;
    }

    $sections = $guide->get('sections');
    $section_count = count($sections);
    $visible_deltas = [];
    foreach ($sections as $delta => $reference) {
      $section = $reference->entity;
      if (!$section || ($section->hasField('field_hide_section') && $section->get('field_hide_section')->value)) {
        continue;
      }
      $visible_deltas[$delta] = TRUE;
    }

    if (count($visible_deltas) === $section_count) {
      // Nothing is hidden, no filtering needed.
      return;
    }

    foreach ($item->getFields() as $field) {
      if (strpos($field->getPropertyPath(), 'sections:') !== 0) {
        continue;
      }
      $values = $field->getValues();
      if (count($values) !== $section_count) {
        // The extracted values don't line up 1:1 with the sections field
        // (e.g. a missing paragraph); skip to avoid mis-filtering.
        continue;
      }
      $field->setValues(array_values(array_intersect_key($values, $visible_deltas)));
    }
  }

}
