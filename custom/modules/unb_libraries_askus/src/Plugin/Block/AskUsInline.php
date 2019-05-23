<?php

namespace Drupal\unb_libraries_askus\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an Inline UNB Libraries AskUs Block.
 *
 * @Block(
 *  id = "askus_inline",
 *  admin_label = @Translation("UNB Libraries AskUs (inline)"),
 *  category = @Translation("UNB Libraries"),
 * )
 */
class AskUsInline extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['askus_inline']['#markup'] = 'AskUs_inline.';

    return $build;
  }

}
