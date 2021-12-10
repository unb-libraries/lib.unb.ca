<?php

namespace Drupal\ior_awards\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ior_awards\Entity\AwardTypeInterface;

/**
 * Default form for IOR award entities.
 */
class AwardForm extends ContentEntityForm {

  /**
   * {@inheritDoc}
   */
  protected function prepareEntity() {
    parent::prepareEntity();
    if (!$this->entity->getSubmission()) {
      $submission = $this
        ->getRouteMatch()
        ->getParameter('ior_submission');
      $this->entity->setSubmission($submission);
    }
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    /** @var \Drupal\ior_awards\Entity\Storage\AwardTypeStorageInterface $storage */
    $storage = $this->entityTypeManager->getStorage('ior_award_type');
    $form['field_type']['widget']['#empty_option'] = $this->t('- Select a value -');
    $form['field_type']['widget']['#options'] = array_map(function (AwardTypeInterface $award_type) {
      return $award_type->label();
    }, $storage->loadUnawarded($this->getContest()->id()));

    return $form;
  }

  /**
   * Get the contest.
   *
   * @return \Drupal\ior\Entity\ContestInterface
   *   A contest entity.
   */
  protected function getContest() {
    return $this->getEntity()
      ->getSubmission()
      ->getContest();
  }

}
