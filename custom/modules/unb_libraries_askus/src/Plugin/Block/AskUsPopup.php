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
    /* $module_handler = \Drupal::service('module_handler'); */
    /* $module_path = $module_handler->getModule('unb_libraries_askus')->getPath(); */

    $html = '
    <div class="askus">
        <h2>Ask Us</h2>
        <noscript><p>Ask Us instant messaging requires JavaScript.</p></noscript>
        <div class="askus-chatbox">
            <a class="askus-popup" href="//ca.libraryh3lp.com/chat/askus@chat.ca.libraryh3lp.com?title=Ask+Us&amp;theme=gota&amp;css=//lib.unb.ca/core/css-2015/libraryh3lp.unb.lib.css">
                Type here to CHAT.
            </a>
        </div>
        <p>
            <a href="/help/ask"><span class="sr-only">Ask by:</span>
                <span><i class="fas fa-envelope"></i> Email</span>
                <span><i class="fas fa-sms"></i> Text</span>
                <span><i class="fas fa-phone"></i> Phone</span>
                <span><i class="fas fa-walking"></i> In-Person</span>
            </a>
        </p>
    </div>
    ';

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

}
