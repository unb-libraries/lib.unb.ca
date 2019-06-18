<?php

namespace Drupal\unb_libraries_askus\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Popup UNB Libraries AskUs Block.
 *
 * @Block(
 *  id = "askus_popup",
 *  admin_label = @Translation("UNB Libraries AskUs (popup)"),
 *  category = @Translation("UNB Libraries"),
 * )
 */
class AskUsPopup extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $chat_header = '<h2><span class="sr-only">Ask Us Chat</span></h2>';
    $chat_widget = _unb_libraries_askus_get_widget('popup');
    $chat_footer =
      '<p class="askus-footer">
        <a href="/help/ask"><span class="sr-only">Ask by:</span>
          <span><i class="fas fa-envelope"></i> Email</span>
          <span><i class="fas fa-sms"></i> Text</span>
          <span><i class="fas fa-phone-alt"></i> Phone</span>
          <span><i class="fas fa-user-alt"></i> In-Person</span>
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
          'chat-popup',
        ],
      ],
    ];
    $render_array['wrapper']['header'] = [
      '#type' => 'markup',
      '#markup' => $chat_header,
    ];
    $render_array['wrapper']['widget'] = [
      '#type' => 'markup',
      '#markup' => $chat_widget,
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
