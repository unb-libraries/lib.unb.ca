<?php

namespace Drupal\ior\Plugin\Menu\LocalTask;

use Drupal\Core\Menu\LocalTaskDefault;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Local task link for submission type "edit" routes.
 */
class SubmissionTypeLocalEditTask extends LocalTaskDefault {

  use StringTranslationTrait;

  /**
   * {@inheritDoc}
   */
  public function getTitle(Request $request = NULL) {
    return $this->t("Edit @submission_type", [
      '@submission_type' => strtolower($request
        ->attributes
        ->get('ior_submission_type')
        ->label())
    ]);
  }

}
