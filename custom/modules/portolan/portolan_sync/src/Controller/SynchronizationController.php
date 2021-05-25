<?php

namespace Drupal\portolan_sync\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\RedirectDestinationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Synchronizes local Portolan collection with data provided by OCLC.
 *
 * @package Drupal\portolan_sync\Controller
 */
class SynchronizationController extends ControllerBase {

  use RedirectDestinationTrait;

  /**
   * Synchronize entities in the database with data received from the source.
   *
   * @return int
   *   The number of synchronized records.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function sync() {
    $count = 0;
    $this->messenger()->addStatus($this->t("@count records have been synchronized.", [
      '@count' => $count,
    ]));

    return new RedirectResponse('/');
  }

}
