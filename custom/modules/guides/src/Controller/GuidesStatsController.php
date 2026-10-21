<?php

namespace Drupal\guides\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\profile\Entity\ProfileInterface;
use Drupal\user\UserInterface;

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

  /**
   * Guide editors may view their own stats, guide admins anyone's.
   */
  public function userStatsAccess(AccountInterface $account, UserInterface $user) {
    if ($account->hasPermission('administer guide entities')) {
      return AccessResult::allowed()->cachePerPermissions();
    }

    return AccessResult::allowedIf($account->hasPermission('create guide entities') && $account->id() == $user->id())
      ->cachePerPermissions()
      ->cachePerUser();
  }

  /**
   * Apply user stats access to the profile owner.
   */
  public function profileStatsAccess(AccountInterface $account, ProfileInterface $profile) {
    $owner = $profile->getOwner();
    if (empty($owner)) {
      return AccessResult::forbidden()->addCacheableDependency($profile);
    }

    return $this->userStatsAccess($account, $owner)->addCacheableDependency($profile);
  }

}
