<?php

namespace Drupal\ior\Entity\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageTrait;
use Drupal\ior\Entity\SubmissionInterface;

/**
 * Entity storage handler for ior_submission entities.
 */
class SubmissionStorage extends SqlContentEntityStorage implements SubmissionStorageInterface {

  use RevisionableEntityStorageTrait;

  /**
   * {@inheritDoc}
   */
  public function loadByContest($contest_id, array $options = []) {
    $options = array_merge([
      'published' => NULL,
    ], $options);

    $query = $this->getQuery()
      ->condition(SubmissionInterface::FIELD_CONTEST, $contest_id);

    if (!is_null($options['published'])) {
      $query->condition('published', $options['published']);
    }

    return $this->loadMultiple($query->execute());
  }

}
