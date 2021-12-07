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
    $chat_footer =
      '<p class="askus-footer">
        <a class="askus-help link-no-hover" href="/help/ask-us"><span class="sr-only">Ask by:</span><span>Email</span>,
          <span>Phone</span>, <span>Text</span>, <span>In-Person</span>
        </a>
      </p>';

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
    $render_array['wrapper']['widget'] = [
      '#type' => 'inline_template',
      '#template' => $chat_widget,
    ];
    $render_array['wrapper']['footer'] = [
      '#type' => 'markup',
      '#markup' => $chat_footer,
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
