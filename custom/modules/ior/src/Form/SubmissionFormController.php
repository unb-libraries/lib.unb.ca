<?php

namespace Drupal\ior\Form;

use Drupal\Core\Controller\FormController;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ior\Entity\ContestInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller responding to (some) "IOR submission" form routes.
 */
class SubmissionFormController {

  use StringTranslationTrait;

  /**
   * The form controller.
   *
   * @var \Drupal\Core\Controller\FormController
   */
  protected $formController;

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Get the form controller.
   *
   * @return \Drupal\Core\Controller\FormController
   *   A form controller object.
   */
  protected function formController() {
    return $this->formController;
  }

  /**
   * Get the route match.
   *
   * @return \Drupal\Core\Routing\RouteMatchInterface
   *   A route match object.
   */
  protected function routeMatch() {
    return $this->routeMatch;
  }

  /**
   * Create a new SubmissionFormController instance.
   *
   * @param \Drupal\Core\Controller\FormController $form_controller
   *   The default form controller.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   A route match.
   */
  public function __construct(FormController $form_controller, RouteMatchInterface $route_match) {
    $this->formController = $form_controller;
    $this->routeMatch = $route_match;
  }

  /**
   * Responds to the 'add-form' route.
   *
   * @param \Drupal\ior\Entity\ContestInterface $contest
   *   A contest entity.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return array
   *   A renderable response.
   */
  public function addForm(ContestInterface $contest, Request $request) {
    if ($contest->isOpen()) {
      return $this->open($contest, $request);
    }
    elseif ($contest->isComingUp()) {
      return $this->comingUp($contest, $request);
    }
    else {
      return $this->closed($contest, $request);
    }
  }

  /**
   * Build a responses for an open contest.
   *
   * @param \Drupal\ior\Entity\ContestInterface $contest
   *   A contest entity.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return array
   *   A renderable response.
   */
  protected function open(ContestInterface $contest, Request $request) {
    return $this
      ->formController()
      ->getContentResult($request, $this->routeMatch());
  }

  /**
   * Build a response for a "not-yet-open" contest.
   *
   * @param \Drupal\ior\Entity\ContestInterface $contest
   *   A contest entity.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return array
   *   A renderable response.
   */
  protected function comingUp(ContestInterface $contest, Request $request) {
    return [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('The contest has not started yet. Submissions will be accepted between @date_open and @date_close.', [
        '@date_open' => $contest->getOpenDate()->format('Y-m-d'),
        '@date_close' => $contest->getCloseDate()->format('Y-m-d'),
      ]),
      '#attributes' => [
        'class' => [
          'alert',
          'alert-warning',
        ],
      ],
    ];
  }

  /**
   * Build a response for a "no-longer-open" contest.
   *
   * @param \Drupal\ior\Entity\ContestInterface $contest
   *   A contest entity.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return array
   *   A renderable response.
   */
  protected function closed(ContestInterface $contest, Request $request) {
    return [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('The contest is closed. Submissions will no longer be accepted.'),
      '#attributes' => [
        'class' => [
          'alert',
          'alert-danger',
        ],
      ],
    ];
  }

}
