<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Dean's Message block.
 *
 * @Block(
 *   id = "deans_message_block",
 *   admin_label = @Translation("Dean's Message"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class DeansMessage extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => [],
      ],
      '#value' => $this->getValue(),
    ];
  }

  /**
   * Gets the Library Hours table structure.
   *
   * @return string
   *   The html structure for library hours (table).
   */
  protected function getValue() {
    // Note: image manually resized to match Content Lg Breakpoint image style.
    $html = '<div class="front-page-border">
      <a href="/about/welcome-libraries">
        <figure class="figcaption-overlay">
            <img src="/modules/custom/lib_core/img/lesley-balcomm-welcome-unb-libraries.png"
             alt="Welcome to UNB Libraries">
            <figcaption class="caption-bottom caption-hover font-size-smaller">Welcome and FAQs &raquo;</figcaption>
        </figure>
      </a>
    </div>';

    return $html;
  }

}
