<?php

namespace Drupal\ior_awards\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ior\Entity\ContestInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for IOR award entities.
 *
 * @package Drupal\ior_awards\Controller
 */
class AwardController extends ControllerBase {

  /**
   * The award storage.
   *
   * @var \Drupal\ior_awards\Entity\Storage\AwardStorageInterface
   */
  protected $awardStorage;

  /**
   * The award view builder.
   *
   * @var \Drupal\Core\Entity\EntityViewBuilderInterface
   */
  protected $awardViewBuilder;

  /**
   * Get the award storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   A storage handler for IOR award entities.
   */
  protected function awardStorage() {
    if (!isset($this->awardStorage)) {
      $this->awardStorage = $this
        ->entityTypeManager()
        ->getStorage('ior_award');
    }
    return $this->awardStorage;
  }

  /**
   * Get the award view builder.
   *
   * @return \Drupal\Core\Entity\EntityViewBuilderInterface
   *   An entity view builder.
   */
  protected function awardViewBuilder() {
    if (!isset($this->awardViewBuilder)) {
      $this->awardViewBuilder = $this
        ->entityTypeManager()
        ->getViewBuilder('ior_award');
    }
    return $this->awardViewBuilder;
  }

  /**
   * List all awards for the given contest.
   *
   * @param \Drupal\ior\Entity\ContestInterface $contest
   *   A contest entity.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current HTTP request.
   *
   * @return array
   *   A renderable list of award entities.
   */
  public function list(ContestInterface $contest, Request $request) {
    $build = [];

    $awards = $this->awardStorage()->loadByContest($contest->id());
    foreach ($awards as $award) {
      $build[] = $this
        ->awardViewBuilder()
        ->view($award);
    }

    return $build;
  }

}
