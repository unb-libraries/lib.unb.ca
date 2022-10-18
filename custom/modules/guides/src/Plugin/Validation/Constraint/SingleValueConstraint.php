<?php

namespace Drupal\guides\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks if the submitted value contains spaces or commas.
 *
 * @Constraint(
 *   id = "SingleValue",
 *   label = @Translation("Single Value", context = "Validation"),
 *   type = "string"
 * )
 */
class SingleValueConstraint extends Constraint {

  /**
   * Invalid value message.
   *
   * @var string
   */
  public $invalid = '%value is not a single value';

}
