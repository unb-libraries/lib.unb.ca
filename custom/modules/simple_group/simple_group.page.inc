<?php

/**
 * @file
 * Contains simple_group.page.inc.
 *
 * Page callback for Simple group entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Simple group templates.
 *
 * Default template: simple_group.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_simple_group(array &$variables) {
  // Fetch SimpleGroup Entity Object.
  $simple_group = $variables['elements']['#simple_group'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
