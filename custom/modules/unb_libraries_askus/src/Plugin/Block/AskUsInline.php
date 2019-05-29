<?php

namespace Drupal\unb_libraries_askus\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an Inline UNB Libraries AskUs Block.
 *
 * @Block(
 *  id = "askus_embedded",
 *  admin_label = @Translation("UNB Libraries AskUs (embedded)"),
 *  category = @Translation("UNB Libraries"),
 * )
 */
class AskUsInline extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $html = _unb_libraries_askus_chat_widget('Instant Message', 'embedded');

    $attachments = [
      'library' => [
        'unb_libraries_askus/askus',
      ],
    ];

    return [
      '#children' => $html,
      '#attached' => $attachments,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    // Disable caching for this block.
    return 0;
  }

}
