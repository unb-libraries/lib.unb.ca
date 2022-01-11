<?php

namespace Drupal\eresources;

/**
 * Base class for an eResources search result.
 *
 * @package Drupal\eresources
 */
class ResultBase {

  /**
   * Encapsulated item object.
   *
   * @var object
   */
  protected $item;

  /**
   * {@inheritDoc}
   */
  public function __construct($item) {
    $this->item = $item;
  }

}
