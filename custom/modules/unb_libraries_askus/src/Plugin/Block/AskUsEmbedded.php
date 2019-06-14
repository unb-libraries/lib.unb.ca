<?php

namespace Drupal\unb_libraries_askus\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an iframe UNB Libraries AskUs Block.
 *
 * @Block(
 *  id = "askus_embedded",
 *  admin_label = @Translation("UNB Libraries AskUs (embedded)"),
 *  category = @Translation("UNB Libraries"),
 * )
 */
class AskUsEmbedded extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $chat_widget = _unb_libraries_askus_get_widget('embedded');

    $render_array[] = [
      '#attached' => [
        'library' => [
          'unb_libraries_askus/askus',
        ],
      ],
    ];
    $render_array['wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => [
          'lh3-chat',
        ],
        'class' => [
          'chat-embedded',
        ],
      ],
    ];
    $render_array['wrapper']['header'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => $this
        ->t('Instant Message'),
    ];
    $render_array['wrapper']['widget'] = [
      '#type' => 'inline_template',
      '#template' => $chat_widget,
    ];

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    // Disable caching for this block.
    return 0;
  }

}
