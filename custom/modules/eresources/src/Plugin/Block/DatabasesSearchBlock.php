<?php

namespace Drupal\eresources\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an 'eResources Article Databases Search' Block.
 *
 * @Block(
 *   id = "databases_search_block",
 *   admin_label = @Translation("Databases Search Block"),
 *   category = @Translation("UNB Libraries eResources"),
 * )
 */
class DatabasesSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\eresources\Form\DatabasesForm');

    return $form;
  }

}
