<?php

namespace Drupal\unb_libraries_askus\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides the UNB Libraries AskUs Block.
 *
 * @Block(
 *  id = "askus_block",
 *  admin_label = @Translation("UNB Libraries AskUs"),
 * )
 */
class AskUs extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $html = '
    <div class="askUs">
        <h2>Ask Us</h2>
        <noscript><p>Ask Us instant messaging requires JavaScript.</p></noscript>
        <div class="askBubble">
            <a class="askPopUp" href="//ca.libraryh3lp.com/chat/askus@chat.ca.libraryh3lp.com?title=Ask+Us&amp;theme=gota&amp;css=//lib.unb.ca/core/css-2015/libraryh3lp.unb.lib.css">Type here to CHAT.</a>
        </div>
        <p>
            <a href="/help/ask.php"><span class="sr-only">Ask by:</span>
                <span><i class="fas fa-envelope"></i> Email</span>
                <span><i class="fas fa-mobile"></i> Text</span>
                <span><i class="fas fa-phone"></i> Phone</span>
                <span><i class="fas fa-user"></i> In-Person</span>
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
