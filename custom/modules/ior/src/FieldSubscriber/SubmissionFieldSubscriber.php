<?php

namespace Drupal\ior\FieldSubscriber;

use Drupal\Core\Field\FieldConfigInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\custom_entity\FieldSubscriber\FieldSubscriberBase;

/**
 * Field subscriber for "ior_submission" entity fields.
 *
 * @package Drupal\ior\FieldSubscriber
 */
class SubmissionFieldSubscriber extends FieldSubscriberBase {

  use StringTranslationTrait;

  /**
   * Alter the "email" field.
   *
   * @param \Drupal\Core\Field\FieldConfigInterface $email
   *   A field config object.
   *
   * @return \Drupal\Core\Field\FieldConfigInterface
   *   The altered field config object.
   */
  protected function alterEmail(FieldConfigInterface $email) {
    $email->addPropertyConstraints('value', [
      'Regex' => [
        'pattern' => '/.*@unb\.ca$/',
        'message' => $this->t('Provide a valid UNB address.'),
      ],
    ]);

    return $email;
  }

}
