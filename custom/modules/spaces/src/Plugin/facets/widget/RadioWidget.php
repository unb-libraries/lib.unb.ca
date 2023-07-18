<?php

namespace Drupal\spaces\Plugin\facets\widget;

use Drupal\facets\Plugin\facets\widget\LinksWidget;

/**
 * The checkbox / radios widget.
 *
 * @FacetsWidget(
 *   id = "radio",
 *   label = @Translation("List of radio buttons"),
 *   description = @Translation("A configurable widget that shows a list of radio buttons"),
 * )
 */
class RadioWidget extends LinksWidget {

  /**
   * {@inheritdoc}
   */
  protected function appendWidgetLibrary(array &$build) {
    $build['#attributes']['class'][] = 'js-facets-radio-links';
    $build['#attached']['library'][] = 'spaces/drupal.facets.radio-widget';
  }

}
