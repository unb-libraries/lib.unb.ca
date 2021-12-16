<?php

namespace Drupal\ior_awards\Controller;

use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\ior\Entity\ContestInterface;
use Drupal\ior_awards\Entity\Storage\AwardStorageInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for IOR award entities.
 *
 * @package Drupal\ior_awards\Controller
 */
class AwardController {

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
    return $this->awardStorage;
  }

  /**
   * Get the award view builder.
   *
   * @return \Drupal\Core\Entity\EntityViewBuilderInterface
   *   An entity view builder.
   */
  protected function awardViewBuilder() {
    return $this->awardViewBuilder;
  }

  /**
   * Create a new AwardController instance.
   *
   * @param \Drupal\ior_awards\Entity\Storage\AwardStorageInterface $award_storage
   *   A storage handler for IOR award entities.
   * @param \Drupal\Core\Entity\EntityViewBuilderInterface $view_builder
   *   An entity view builder.
   */
  public function __construct(AwardStorageInterface $award_storage, EntityViewBuilderInterface $view_builder) {
    $this->awardStorage = $award_storage;
    $this->awardViewBuilder = $view_builder;
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
