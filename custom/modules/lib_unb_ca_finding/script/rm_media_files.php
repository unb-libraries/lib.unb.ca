<?php

use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;

/**
 * Deletes all media entities and their associated files.
 */
function delete_all_media_and_files() {
  // Load all media entities.
  $media_ids = \Drupal::entityQuery('media')
    ->execute();

  // Load each media entity and delete it along with associated files.
  foreach ($media_ids as $media_id) {
    $media = Media::load($media_id);

    if ($media) {
      // Get the file entity associated with the media.
      $file = $media->get('field_media_image')->entity;
      if ($file) {
        // Delete the file entity.
        $file->delete();
      }
      // Delete the media entity.
      $media->delete();
    }
  }

  // Load all file entities that might not be associated with media entities.
  $file_ids = \Drupal::entityQuery('file')
    ->execute();

  // Delete each file entity.
  foreach ($file_ids as $file_id) {
    $file = File::load($file_id);
    if ($file) {
      $file->delete();
    }
  }
}

// Run the function to delete all media and files.
delete_all_media_and_files();