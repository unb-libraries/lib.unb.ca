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
    $profile = $user->public_profiles->get(0)->entity;
    $this->setObject($profile);

    $form += parent::buildForm($form, $form_state);

    $form['#title'] = 'Statistics for ' . $user->field_first_name->getString() . ' ' . $user->field_last_name->getString();
    return $form;
  }

}
