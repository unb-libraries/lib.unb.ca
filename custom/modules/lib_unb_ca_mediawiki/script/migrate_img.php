<?php

use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

function createMediaImageFromUrl($imageUrl) {
    echo "\n$imageUrl\n";
    
    // Download the image from the provided URL
    $fileContents = file_get_contents($imageUrl);
    
    if ($fileContents === FALSE) {
        die("Error: Unable to download the image from the URL.");
    }

    // Generate a unique file name
    $fileName = basename(parse_url($imageUrl, PHP_URL_PATH));
    
    // Create a temporary file
    $fileRepository = \Drupal::service('file.repository');
    $managedFile = $fileRepository->writeData($fileContents, 'public://' . $fileName, FileSystemInterface::EXISTS_REPLACE);
    // $managedFile = file_save_data($fileContents, 'temporary://' . $fileName, FILE_EXISTS_REPLACE);
    
    if ($managedFile === FALSE) {
        die("Error: Unable to save the temporary file.");
    }
    
    // Move the temporary file to the public file system
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
            // 'alt' => 'Alt text for the image',  // Replace with the appropriate alt text
            'title' => $filename,  // Replace with the appropriate title
        ],
    ]);
    $media->save();
    
    // Return the media entity ID
    return $media->id();
}

function parseWebPage($url) {
    echo "\n$url\n";
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

    // Extract the 2nd link from each <td> with class TablePager_col_img_name
    $links = $xpath->query("//td[@class='TablePager_col_img_name']/a[2]/@href");
    
    // Iterate over the links and create media
    $limit = $extra[0];
    $i = 0;

    foreach ($links as $link) {
      $imageUrl = 'https://unbhistory.lib.unb.ca' . $link->nodeValue;
      $mediaId = createMediaImageFromUrl($imageUrl);
      echo "Media image created with ID: $mediaId\n";
      $i ++;

      if ($limit and $i == $limit - 1) {
        return;
      }
    }

    return;
}

$url = 'https://unbhistory.lib.unb.ca/Special:ListFiles?limit=500';
parseWebPage($url);