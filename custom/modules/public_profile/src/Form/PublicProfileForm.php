<?php

namespace Drupal\public_profile\Form;

use Drupal\profile\Form\ProfileForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Override for public profile form.
 */
class PublicProfileForm extends ProfileForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);
    $profile = $this->entity;
    if ($profile->bundle() == 'public') {
      $form_state->setRedirect('entity.profile.canonical', [
        'profile' => $profile->id(),
      ]);
    }
  }

}
