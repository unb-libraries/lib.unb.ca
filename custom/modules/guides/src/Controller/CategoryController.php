<?php

namespace Drupal\guides\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;

/**
 * Category controller object.
 */
class CategoryController extends ControllerBase {

  /**
   * Page title for resources in category by type.
   */
  public function pageTitle(EntityInterface $guide_category, $type) {
    $name = $guide_category->label();

    if ($type == 'databases') {
      return "Article & Research Databases for {$name}";
    }
    else {
      return "Reference Materials for {$name}";
    }
  }

  /**
   * List resources for the category by type.
   */
  public function viewResources(EntityInterface $guide_category, $type) {
    $build = [];

    if ($type == 'databases') {
      $build['#markup'] = '<p>Use article databases to find articles, reviews, book chapters, etc.</p>';
      $build['eresources'] = [
        '#theme' => 'ckeditor-eresources',
        '#resources' => $guide_category->getEresourcesByType('DATA'),
        '#options' => ['keyresources' => 99999],
      ];
    }
    else {
      $build['#markup'] = '<p>Find dictionaries, encyclopedias, handbooks, and other reference materials</p>';
      $build['eresources'] = [
        '#theme' => 'ckeditor-eresources',
        '#resources' => $guide_category->getEresourcesByType('REF'),
        '#options' => ['keyresources' => 99999],
      ];
    }

    return $build;
  }

}
