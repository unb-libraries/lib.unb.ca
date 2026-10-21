<?php

namespace Drupal\guides\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\guides\Form\StatsFormBase;
use Drupal\user\UserInterface;

/**
 * Display Google Analytics stats data.
 */
class UserStatsForm extends StatsFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormType() {
    return 'user';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, UserInterface $user = NULL) {
    $form['#title'] = 'Statistics for ' . $user->field_first_name->getString() . ' ' . $user->field_last_name->getString();

    // Blocked users and users without the public_profile role have no
    // active profile.
    $profile = $user->public_profiles->isEmpty() ? NULL : $user->public_profiles->get(0)->entity;
    if (empty($profile)) {
      $form['header'] = [
        '#markup' => '<div class="alert alert-info" role="alert"><p><i class="fa fa-info-circle" aria-hidden="true"></i> This user does not have an active public profile, so there are no stats to display.</p></div>',
      ];
      return $form;
    }

    $this->setObject($profile);

    $form += parent::buildForm($form, $form_state);

    return $form;
  }

}
