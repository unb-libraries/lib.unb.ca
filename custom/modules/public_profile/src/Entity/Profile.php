<?php

namespace Drupal\public_profile\Entity;

use Drupal\profile\Entity\Profile as CoreProfile;

/**
 * Override of the Profile class.
 */
class Profile extends CoreProfile {

  /**
   * {@inheritDoc}
   */
  public function label() {
    if ($this->bundle() != 'public') {
      return parent::label();
    }

    $account = $this->uid->entity;
    return implode(' ', [
      $account->field_first_name->value,
      $account->field_last_name->value,
    ]);
  }

}
