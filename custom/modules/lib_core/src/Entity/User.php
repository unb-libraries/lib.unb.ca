<?php

namespace Drupal\lib_core\Entity;

use Drupal\user\Entity\User as CoreUser;

/**
 * Override of the core User class.
 */
class User extends CoreUser {

  /**
   * {@inheritDoc}
   */
  public function label() {
    return $this->field_first_name->value . ' ' . $this->field_last_name->value . ' [' . $this->getDisplayName() . ']';
  }

}
