<?php

namespace Drupal\ior\Entity\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageTrait;

/**
 * Storage handler for contest entities.
 */
class ContestStorage extends SqlContentEntityStorage {

  use RevisionableEntityStorageTrait;

}
