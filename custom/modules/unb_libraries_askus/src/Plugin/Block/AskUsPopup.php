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
    $askus_src = 'https://ca.libraryh3lp.com/chat/askus
      @chat.ca.libraryh3lp.com?title=Ask+Us&amp;theme=gota';
    $askus_src .= '&amp;css=https://lib.unb.ca/core/css-2015/libraryh3lp.unb.lib.css';
    $chat_widget =
      '<h2><span class="sr-only">Ask Us Chat</span></h2>
      <div class="requires-js">
        <div id="lh3-online" style="display:none;">
          <a href="' . $askus_src . '">Type here to CHAT.</a>
        </div>
        <div id="lh3-offline" style="display:none;">
          Ask Us is currently <strong>offline</strong>.
        </div>
        <div id="lh3-away" style="display: none;">
          Ask Us is currently busy serving other patrons.
        </div>
        <div id="lh3-busy" style="display: none;">
          Ask Us is currently busy serving other patrons.
        </div>
      </div>
      <div id="lh3-noscript">
        Ask Us chat requires JavaScript.
      </div>';
    $chat_footer =
      '<p class="askus-footer">
        <a href="/help/ask"><span class="sr-only">Ask by:</span>
          <span><i class="fas fa-envelope"></i> Email</span>
          <span><i class="fas fa-sms"></i> Text</span>
          <span><i class="fas fa-phone"></i> Phone</span>
          <span><i class="fas fa-walking"></i> In-Person</span>
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
