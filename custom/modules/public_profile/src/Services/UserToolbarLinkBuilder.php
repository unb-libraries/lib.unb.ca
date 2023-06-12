<?php

namespace Drupal\public_profile\Services;

use Drupal\Core\Url;
use Drupal\user\ToolbarLinkBuilder;

/**
 * User toolbar link overrides.
 */
class UserToolbarLinkBuilder extends ToolbarLinkBuilder {

  /**
   * {@inheritdoc}
   */
  public function renderToolbarLinks() {
    $build = parent::renderToolbarLinks();

    $build['#links']['account']['title'] = $this->t('View public profile');
    $build['#links']['account']['url'] = Url::fromRoute('public_profile.view', [
      'user' => $this->account->id(),
    ]);

    $build['#links']['account_edit']['title'] = $this->t('Edit public profile');
    $build['#links']['account_edit']['url'] = Url::fromRoute('profile.user_page.single', [
      'user' => $this->account->id(),
      'profile_type' => 'public',
    ]);

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['renderToolbarLinks'];
  }

}
