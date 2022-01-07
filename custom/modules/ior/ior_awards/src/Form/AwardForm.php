<?php

namespace Drupal\ior_awards\Form;

use Drupal\Core\Entity\ContentEntityForm;

/**
 * Default form for IOR award entities.
 */
class AwardForm extends ContentEntityForm {

  /**
   * {@inheritDoc}
   */
  protected function prepareEntity() {
    parent::prepareEntity();
    if (!$this->entity->getContest()) {
      $contest = $this
        ->getRouteMatch()
        ->getParameter('contest');
      $this->entity->setContest($contest);
    }
  }

}
