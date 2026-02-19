<?php

/**
 * @file
 * Contains rec_count.php.
 */

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Function to query all nodes of a specific content type.
 *
 * @param string $content_type
 *   The content type to search for.
 *
 * @return \Drupal\node\Entity\Node[]
 *   An array of node entities.
 */
function getAllNodesOfContentType($content_type) {
  // Load all nodes of the specified content type.
  $nids = \Drupal::entityQuery('node')
    ->condition('type', $content_type)
    ->execute();

  // Load node entities.
  $nodes = Node::loadMultiple($nids);

  return $nodes;
}

$pages = getAllNodesOfContentType('library_page');

if ($pages) {
  $i = 0;

  foreach ($pages as $page) { 
    if (str_contains($page->path->alias, 'archives/finding')) { 
      $i++;
    }
  }
  echo "\FINDING AIDS RECORDS: $i\n";
}
