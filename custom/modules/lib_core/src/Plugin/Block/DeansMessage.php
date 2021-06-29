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
        'class' => [
          'd-flex',
          'front-page-border',
          'bg-secondary',
        ],
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
    $html = '
      <a href="/what-to-expect">
        <figure class="caption-overlay-bottom">
            <img src="/modules/custom/lib_core/img/deans-message.png"
              alt="Welcome to UNB Libraries">
            <figcaption>Covid-19 & Fall 2021</figcaption>  
        </figure>
      </a>
    ';

    return $html;
  }

}
