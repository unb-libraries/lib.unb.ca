<?php

namespace Drupal\guides\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\profile\Entity\ProfileInterface;

/**
 * Provides route responses for guides stats.
 */
class GuidesStatsController extends ControllerBase {

  /**
   * Redirect to user stats from profile entity.
   */
  public function redirectFromProfile(ProfileInterface $profile) {
    $user = $profile->uid->entity;
    return $this->redirect('guides.user_stats', ['user' => $user->id()]);
  }

}
