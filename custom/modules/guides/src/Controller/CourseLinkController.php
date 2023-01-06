<?php

namespace Drupal\guides\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides route responses for course_link entities.
 */
class CourseLinkController extends ControllerBase {

  /**
   * Collection page title.
   *
   * @param \Drupal\Core\Entity\EntityInterface $guide
   *   An entity.
   *
   * @return string
   *   The title.
   */
  public function pageTitle(EntityInterface $guide) {
    return 'Course Linking for ' . $guide->label();
  }

}
