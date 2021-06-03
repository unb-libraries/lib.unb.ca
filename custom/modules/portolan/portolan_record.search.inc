<?php

/**
 * @file
 * Contains portolan_record.search.inc.
 *
 * Page callback for "Portolan record" entities.
 */

/**
 * Prepares variables for portolan_record templates.
 *
 * Default template: portolan-record.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_portolan_record(array &$variables) {
  $variables['portolan_record'] = $variables['elements']['#portolan_record'];
  $variables['search_api_excerpt'] = $variables['elements']['#search_api_excerpt'];
}
