<?php

namespace Drupal\eresources\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an 'eResources e-Reference Materials Search' Block.
 *
 * @Block(
 *   id = "reference_search_block",
 *   admin_label = @Translation("E-reference Materials Search Block"),
 *   category = @Translation("UNB Libraries eResources"),
 * )
 */
class ReferenceSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\eresources\Form\ReferenceForm');

    return $form;
  }

}
