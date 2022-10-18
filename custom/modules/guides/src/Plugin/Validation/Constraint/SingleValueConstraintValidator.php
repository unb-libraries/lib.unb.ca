<?php

namespace Drupal\guides\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the SingleValue constraint.
 */
class SingleValueConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    foreach ($value as $item) {
      if (preg_match('/[\s,]/', $item->value)) {
        $this->context->addViolation($constraint->invalid, ['%value' => $item->value]);
      }
    }
  }

}
