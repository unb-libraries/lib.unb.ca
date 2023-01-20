<?php

namespace Drupal\guides\Entity\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageInterface;
use Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageTrait;

/**
 * Storage handler for "guide_category" entities.
 */
class GuideCategoryStorage extends SqlContentEntityStorage implements RevisionableEntityStorageInterface {

  use RevisionableEntityStorageTrait;

}
