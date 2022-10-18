<?php

namespace Drupal\guides\Form;

use Drupal\Core\Entity\ContentEntityForm;

/**
 * Default form for "course_link" entities.
 */
class CourseLinkForm extends ContentEntityForm {

  /**
   * Assign guide, if not already assigned.
   */
  protected function prepareEntity() {
    parent::prepareEntity();
    if (!$this->entity->guide->entity) {
      $guide = $this
        ->getRouteMatch()
        ->getParameter('guide');
      $this->entity->set('guide', $guide);
    }
  }

}
