<?php

namespace Drupal\ior\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;

/**
 * Default form for "submission" entities.
 */
class SubmissionForm extends ContentEntityForm {

  /**
   * Assign contest, if not already assigned.
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

  /**
   * {@inheritDoc}
   */
  public function getEntityFromRouteMatch(RouteMatchInterface $route_match, $entity_type_id) {
    if ($contest = $route_match->getParameter('contest')) {
      /** @var \Drupal\ior\Entity\ContestInterface $contest */
      $route_match->getParameters()->add([
        'ior_submission_type' => $contest->getSubmissionType(),
      ]);
      $route_match->getRawParameters()->add([
        'ior_submission_type' => $contest->getSubmissionType()->id(),
      ]);
    }
    $entity = parent::getEntityFromRouteMatch($route_match, $entity_type_id);
    return $entity;
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form['terms_conditions_accepted']['widget']['value']['#title'] = $this->t('I certify that I am the copyright owner of the submitted image and have the necessary rights, permissions, and/or licenses to submit the image to the competition according to the full @link_to_contest_rules.', [
      '@link_to_contest_rules' => $this->buildContestRulesLink()->toString(),
    ]);

    return $form;
  }

  /**
   * Build a "contest rules" link.
   *
   * @return \Drupal\Core\Link
   *   A renderable link object.
   */
  protected function buildContestRulesLink() {
    return Link::fromTextAndUrl(
      $this->t('contest rules and conditions'),
      $this->getEntity()
        ->getContest()
        ->toUrl(),
    );
  }

  /**
   * {@inheritDoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Submit');
    return $actions;
  }

  /**
   * {@inheritDoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $return = parent::save($form, $form_state);
    if ($return === SAVED_NEW) {
      $this->created($form, $form_state);
    }
    else {
      $this->updated($form, $form_state);
    }
    return $return;
  }

  /**
   * Handle a successful entity create submission.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  protected function created(array $form, FormStateInterface $form_state) {
    $contest = $this->getEntity()->getContest();
    $form_state->setRedirectUrl($contest->toUrl());
    $this->messenger()->addStatus($this->t('
      <p>Thank your for your submission. A confirmation email has been sent to @email.</p>
      <p>This competition is a new collaboration of the @grad_link and the Harriet Irving Library Research Commons.</p>', [
        '@email' => $this->getEntity()->getEmail(),
        '@grad_link' => Link::fromTextAndUrl(
          'School of Graduate Studies',
          Url::fromUri('https://www.unb.ca/gradstudies/', [
            'target' => '_blank',
          ])
        )->toString(),
      ]
    ));
  }

  /**
   * Handle successful entity update submission.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  protected function updated(array $form, FormStateInterface $form_state) {
    $form_state->setRedirectUrl($this->getEntity()->toUrl());
    $this->messenger()->addStatus($this->t('The submission has been updated.'));
  }

}
