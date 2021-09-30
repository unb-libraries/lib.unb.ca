<?php

namespace Drupal\records_management\Entity\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageInterface;
use Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageTrait;

/**
 * Storage handler for "Retention schedule" entities.
 */
class ScheduleStorage extends SqlContentEntityStorage implements RevisionableEntityStorageInterface {

  use RevisionableEntityStorageTrait;

}
