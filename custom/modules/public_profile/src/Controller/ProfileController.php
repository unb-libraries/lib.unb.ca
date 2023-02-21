<?php

namespace Drupal\public_profile\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\profile\Entity\ProfileInterface;

/**
 * Provides route responses for the public_profile module.
 */
class ProfileController extends ControllerBase {

  /**
   * Redirect to profile view from user entity.
   */
  public function view(AccountInterface $user) {
    $profile = $user->public_profiles->get(0)->entity;
    return $this->redirect('entity.profile.canonical', ['profile' => $profile->id()]);
  }

  /**
   * Redirect to user edit from profile entity.
   */
  public function editUser(ProfileInterface $profile) {
    $user = $profile->uid->entity;
    return $this->redirect('entity.user.edit_form', ['user' => $user->id()]);
  }

}
