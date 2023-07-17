<?php

namespace Drupal\spaces\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Defines the interface for "Space" entities.
 */
interface SpaceInterface extends ContentEntityInterface {

  /**
   * Whether the space features at least one image.
   *
   * @return bool
   *   TRUE if at least one image has been attached to this space.
   *   FALSE otherwise.
   */
  public function hasImages();

}
