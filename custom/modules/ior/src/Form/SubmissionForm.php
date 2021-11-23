<?php

namespace Drupal\ior\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
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
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    if ($this->getEntity()->isNew()) {
      $form['agree_contest_rules'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('I certify that I am the copyright owner of the submitted image and have the necessary rights, permissions, and/or licenses to submit the image to the competition according to the full @link_to_contest_rules.', [
          '@link_to_contest_rules' => $this->buildContestRulesLink()->toString(),
        ]),
        '#required' => TRUE,
        '#default_value' => FALSE,
        '#weight' => 97,
      ];
    }

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
      Url::fromUri($this->getContestRulesUri(), [
        'attributes' => [
          'target' => '_blank',
        ],
      ])
    );
  }

  /**
   * Return the URI of the "Contest rules" page.
   *
   * @return string
   *   A string.
   */
  protected function getContestRulesUri() {
    $scheme_and_host = $this->getRequest()->getSchemeAndHttpHost();
    return "{$scheme_and_host}/researchcommons/ior";
  }

}
