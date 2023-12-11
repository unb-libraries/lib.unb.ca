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
    $html = '
      <a href="/oa-policy">
        <figure class="figcaption-overlay front-page-border">
            <img src="/modules/custom/lib_core/img/towards_open_update.jpg"
             alt="Towards Open: Learn more about UNB\'s newly-adopted Open Access Policy and how the Library can support your publishing.">
            <figcaption class="caption-bottom caption-hover font-size-smaller">Review the new policy &raquo;</figcaption>
        </figure>
      </a>
    ';

    return $html;
  }

}
