<?php

namespace Drupal\ior\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Default form for "submission" entities.
 */
class SubmissionForm extends ContentEntityForm {

  /**
   * The contest.
   *
   * @var \Drupal\ior\Entity\ContestInterface
   */
  protected $contest;

  /**
   * Get the contest.
   *
   * @return \Drupal\ior\Entity\ContestInterface
   *   A contest entity.
   */
  protected function getContest() {
    if (!$this->contest) {
      $this->contest = $this
        ->getRouteMatch()
        ->getParameter('contest');
    }
    return $this->contest;
  }

  /**
   * {@inheritDoc}
   */
  public function buildEntity(array $form, FormStateInterface $form_state) {
    $entity = parent::buildEntity($form, $form_state);
    $entity->setContest($this->getContest());
    return $entity;
  }

}
