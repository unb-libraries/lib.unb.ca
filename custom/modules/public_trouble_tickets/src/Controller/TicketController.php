<?php

namespace Drupal\public_trouble_tickets\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides route responses for the public_trouble_tickets module.
 */
class TicketController extends ControllerBase {

  /**
   * Generate a title for ticket view.
   *
   * @param int $id
   *   The case id.
   *
   * @return string
   *   The title.
   */
  public function viewTitle(int $id) {
    return "Trouble Ticket #{$id}";
  }

  /**
   * Ticket view.
   *
   * @param int $id
   *   The case id.
   *
   * @return array
   *   Render array.
   */
  public function view(int $id) {
    $fogbugz = \Drupal::service('fogbugz_api.manager');
    $case = $fogbugz->getCase($id);

    if (!$case) {
      throw new NotFoundHttpException();
    }

    $new = FALSE;
    $statuses = $this->messenger()->messagesByType('status');
    if (!empty($statuses)) {
      foreach ($statuses as $status) {
        if (preg_match("/$id/", $status)) {
          $new = TRUE;
          break;
        }
      }
    }

    return [
      '#theme' => 'view',
      '#case' => $case,
      '#new' => $new,
      '#askus' => $this->getAskUsSidebar(),
    ];
  }

  /**
   * Status page.
   *
   * @return array
   *   Render array.
   */
  public function serviceStatus() {
    $fogbugz = \Drupal::service('fogbugz_api.manager');
    $alerts = $fogbugz->getActiveAlerts();

    return [
      '#theme' => 'status',
      '#alerts' => $alerts,
      '#askus' => $this->getAskUsSidebar(),
      '#attached' => [
        'library' => [
          'public_trouble_tickets/status-v2',
        ],
      ],
    ];
  }

  /**
   * Active tickets.
   *
   * @return array
   *   Render array.
   */
  public function list() {
    $fogbugz = \Drupal::service('fogbugz_api.manager');
    $alerts = $fogbugz->getActiveAlerts();
    $cases = $fogbugz->searchCases('tag:"trouble_ticket_eresources" -status:"closed"');

    usort($cases, function ($a, $b) {
      return $b->getDateOpened() <=> $a->getDateOpened();
    });

    return [
      '#theme' => 'list',
      '#alerts' => $alerts,
      '#cases' => $cases,
      '#askus' => $this->getAskUsSidebar(),
      '#attached' => [
        'library' => ['public_trouble_tickets/datatables'],
      ],
    ];
  }

  /**
   * Ask us sidebar block.
   *
   * @return array
   *   Render array.
   */
  private function getAskUsSidebar() {
    $blockManager = \Drupal::service('plugin.manager.block');
    $askUs = $blockManager->createInstance('askus_popup', []);
    $access = $askUs->access(\Drupal::currentUser());

    if (is_object($access) && $access->isForbidden() || is_bool($access) && !$access) {
      return [];
    }
    return $askUs->build();
  }

}
