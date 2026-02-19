<?php

/**
 * @file
 * Contains fix_sidebar.php.
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

$new_bid = 116;
$block_storage = \Drupal::entityTypeManager()->getStorage('block_content');
$block = $block_storage->load($new_bid);
$pages = getAllNodesOfContentType('library_page');

if ($block) {
  $uuid = $block->uuid();
  $plugin_id = "block_content:$uuid";

  foreach ($pages as $page) { 
    if (str_contains($page->path->alias, 'archives/finding')) { 
      $pid = $page->field_page_content->entity->field_column_2->getValue()[1]['target_id'];
      $paragraph = Paragraph::load($pid);
      
      if ($paragraph->field_selected_block) {
        $paragraph->field_selected_block->plugin_id = $plugin_id;

        $paragraph->field_selected_block->settings = [
          'id' => $plugin_id,
          'label' => 'Archives & Special Collections Sidebar',
          'label_display' => false,
          'provider' => 'block_content',
          'status' => true,
          'info' => '',
          'view_mode' => 'full',
        ];
        
        echo "\nUpdating paragraph [$pid]\n";
        $paragraph->save();  
      }
    }
  }
}
