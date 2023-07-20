<?php

namespace Drupal\spaces\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\custom_entity\Entity\UserCreatedInterface;
use Drupal\custom_entity\Entity\UserEditedInterface;

/**
 * Defines the interface for "Space" entities.
 */
interface SpaceInterface extends ContentEntityInterface, UserCreatedInterface, UserEditedInterface {

  /**
   * Get the parent space, if present.
   *
   * @return \Drupal\spaces\Entity\SpaceInterface|null
   *   A "space" entity.
   */
  public function getParent();

  /**
   * Whether the space features at least one image.
   *
   * @return bool
   *   TRUE if at least one image has been attached to this space.
   *   FALSE otherwise.
   */
  public function hasImages();

}
