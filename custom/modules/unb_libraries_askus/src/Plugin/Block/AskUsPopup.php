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
        <div class="askus-header" xmlns="http://www.w3.org/1999/html">
          <div class="askus-heading"><h2 class="border-bottom-0"><span class="sr-only">Ask Us</span></h2></div>
          <div class="askus-icon"><i class="fas fa-comments text-dark"></i></div>
        </div>';
    $chat_widget = _unb_libraries_askus_get_widget('popup');
    $chat_footer =
      '<p class="askus-footer">
        <a class="askus-help" href="/help/ask-us">
          <span class="sr-only">Ask by:</span><span>Phone</span>,
          <span>Text</span>, <span>Email</span>, <span>In-Person</span>
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
          'px-2',
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
