<?php

namespace Drupal\eresources\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an 'eResources Videos Search' Block.
 *
 * @Block(
 *   id = "videos_search_block",
 *   admin_label = @Translation("Videos Search Block"),
 *   category = @Translation("UNB Libraries eResources"),
 * )
 */
class VideosSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\eresources\Form\VideosForm');

    return $form;
  }

}
