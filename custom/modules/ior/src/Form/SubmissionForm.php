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
  public function save(array $form, FormStateInterface $form_state) {
    if ($saved = parent::save($form, $form_state)) {
      $contest = $this->getContest();
      /** @var \Drupal\ior\Entity\SubmissionInterface $submission */
      $submission = $this->getEntity();
      $contest->addSubmission($submission);
      $contest->save();
    }
    return $saved;
  }

}
