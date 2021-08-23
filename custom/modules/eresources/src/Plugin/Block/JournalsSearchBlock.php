<?php

namespace Drupal\eresources\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an 'eResources Journal Search' Block.
 *
 * @Block(
 *   id = "journals_search_block",
 *   admin_label = @Translation("Journals Search Block"),
 *   category = @Translation("UNB Libraries eResources"),
 * )
 */
class JournalsSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\eresources\Form\JournalsForm');

    return $form;
  }

}
