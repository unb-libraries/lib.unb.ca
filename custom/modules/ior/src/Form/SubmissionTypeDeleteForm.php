<?php

namespace Drupal\ior\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Entity\EntityDeleteFormTrait;

/**
 * Delete form for "Submission type" entities.
 */
class SubmissionTypeDeleteForm extends EntityConfirmFormBase {

  use EntityDeleteFormTrait;

}
