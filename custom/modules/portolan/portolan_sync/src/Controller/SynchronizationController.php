<?php

namespace Drupal\portolan_sync\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\RedirectDestinationTrait;
use Drupal\portolan_sync\DataImporterInterface;
use Drupal\portolan_sync\Synchronization\DataSynchronizerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Synchronizes local Portolan collection with data provided by OCLC.
 *
 * @package Drupal\portolan_sync\Controller
 */
class SynchronizationController extends ControllerBase {

  use RedirectDestinationTrait;

  /**
   * The synchronizer.
   *
   * @var \Drupal\Synchronization\DataSynchronizerInterface
   */
  protected $synchronizer;

  /**
   * Get the synchronizer.
   *
   * @return \Drupal\Synchronization\DataSynchronizerInterface
   *   A data synchronizer.
   */
  protected function synchronizer() {
    return $this->synchronizer;
  }

  /**
   * Creates a SynchronizationController instance.
   *
   * @param \Drupal\portolan_sync\Synchronization\DataSynchronizerInterface $synchronizer
   *   A data synchronizer.
   */
  public function __construct(DataSynchronizerInterface $synchronizer) {
    $this->synchronizer = $synchronizer;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('portolan.oclc_synchronizer')
    );
  }

  /**
   * Synchronize entities in the database with data received from the source.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The number of synchronized records.
   */
  public function sync() {
    $records_synced = 0;
    $this->messenger()->addStatus($this->t("@count records have been synchronized.", [
      '@count' => $records_synced,
    ]));

    return new RedirectResponse('/');
  }

}
