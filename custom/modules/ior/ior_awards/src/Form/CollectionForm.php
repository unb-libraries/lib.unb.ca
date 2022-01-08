<?php

namespace Drupal\ior_awards\Form;

use Drupal\Core\Entity\ContentEntityForm;

/**
 * Default form for IOR collection entities.
 */
class CollectionForm extends ContentEntityForm {

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
