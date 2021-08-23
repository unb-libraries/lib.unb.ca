<?php

namespace Drupal\eresources\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an 'eResources eBooks Search' Block.
 *
 * @Block(
 *   id = "ebooks_search_block",
 *   admin_label = @Translation("eBooks Search Block"),
 *   category = @Translation("UNB Libraries eResources"),
 * )
 */
class EbooksSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\eresources\Form\EbooksForm');

    return $form;
  }

}
