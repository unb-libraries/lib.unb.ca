<?php

/**
 * @file
 * Contains ls_paragraph.php.
 */

use Drupal\paragraphs\Entity\Paragraph;

ls_entities('paragraph');

function ls_entities($type) {
  // Load all paragraph entities.
  $pids = \Drupal::entityQuery('paragraph')->execute();
  // Load paragraph entities.
  $paragraphs = Paragraph::loadMultiple($pids);

  foreach ($paragraphs as $entity) {
    $id = $entity->id();
    echo "\nFound pragraph with ID [$id]\n";
  }
}
