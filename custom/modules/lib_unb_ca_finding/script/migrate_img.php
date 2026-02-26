<?php

use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

$directoryName = 'sites/default/files/finding-aids';

// Check if the directory already exists
if (!is_dir($directoryName)) {
    // Create the directory
    if (mkdir($directoryName, 0755, true)) {
        echo "Directory created successfully.\n";
    } else {
        echo "Failed to create directory.\n";
    }
} else {
    echo "Directory already exists.\n";
}

$url = DRUPAL_ROOT . '/modules/custom/lib_unb_ca_finding/data/finding-import-imgs.html';
parseWebPage($url);

function parseWebPage($url) {
    // Initialize a new DOMDocument
    $dom = new DOMDocument();

    // Suppress errors due to malformed HTML
    libxml_use_internal_errors(true);

    // Load the HTML from the URL
    $html = file_get_contents($url);
    $dom->loadHTML($html);

    // Clear any libxml errors
    libxml_clear_errors();

    // Initialize a new DOMXPath instance
    $xpath = new DOMXPath($dom);

    // Extract attribute src of every image
    $imgs = $xpath->query("//img/@src");
    
    // Iterate over the links and create media
    $limit = $_SERVER['argv'][3];
    $i = 0;
        
    foreach ($imgs as $img) {
      $imageUrl = $img->nodeValue;
      $mediaId = createMediaImageFromUrl($imageUrl);

      if ($mediaId) {
        echo "Media image created with ID: $mediaId\n";
        $i ++;
      }

      if ($limit and $i == $limit) {
        return;
      }
    }

    return;
}

function createMediaImageFromUrl($imageUrl) {    
    // Download the image from the provided URL
    $fileContents = file_get_contents($imageUrl);
    
    if ($fileContents === FALSE) {
        die("Error: Unable to download the image from the URL.");
    }

    // Generate a unique file name
    $fileName = basename(parse_url($imageUrl, PHP_URL_PATH));

    // Create a managed file
    $fileRepository = \Drupal::service('file.repository');
    $managedFile = $fileRepository->writeData($fileContents, 'public://finding-aids/' . $fileName, FileSystemInterface::EXISTS_REPLACE);
    
    if ($managedFile === FALSE) {
        die("Error: Unable to save the file.");
    }
    
    // Move the managed file to the public file system
    $file = File::create([
        'uri' => $managedFile->getFileUri(),
        'status' => 1,
    ]);
    $file->save();

    // Create the media entity
    $media = Media::create([
      'bundle' => 'image',  // Ensure 'image' is the correct media type bundle for your setup
      'name' => $fileName,
      'status' => 1,
      'field_media_image' => [
        'target_id' => $file->id(),
        'title' => $filename,  
      ],
    ]);
    $media->save();
    
    // Return the media entity ID
    return $media->id();

  return;
}

/**
 * Retrieves all file filenames in the Drupal site.
 */
function getAllFileFilenames() {
    // Load all file entities.
    $file_ids = \Drupal::entityQuery('file')
        ->execute();

    $filenames = [];
    foreach ($file_ids as $file_id) {
        $file = File::load($file_id);
        if ($file) {
            $filenames[] = $file->getFilename();
        }
    }

    return $filenames;
}