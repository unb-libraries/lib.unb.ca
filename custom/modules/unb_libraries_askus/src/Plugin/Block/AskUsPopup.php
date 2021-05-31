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
    $chat_header = '
        <div class="d-flex mt-3">
          <div class="flex-grow-1"><h2 class="border-bottom-0 text-black">ASK US</h2></div>
          <div><i class="fas fa-comments fa-3x text-dark"></i></div>
        </div>';
    $chat_widget = _unb_libraries_askus_get_widget('popup');
    $chat_footer =
      '<p class="askus-footer">
        <a class="askus-help" href="/help/ask-us"><span class="sr-only">Ask by:</span>
          Phone, Text, Email <span class="d-none">, In-Person</span>
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

}
