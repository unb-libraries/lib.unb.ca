<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides the UNB Libraries AskUsBlock.
 *
 * @Block(
 *   id = "askus_block",
 *   admin_label = @Translation("UNB Libraries AskUs"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class QuickLinks extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $askus = [
      [
        '#wrapper_attributes' => [
          'class' => [
            'askPopUp',
          ],
        ],
        '#children' => Link::fromTextAndUrl(
          $this->t('Renew Books'),
          Url::fromUri('https://ca.libraryh3lp.com/chat/askus@chat.ca.libraryh3lp.com?title=Ask+Us&theme=gota&css=//lib.unb.ca/core/css-2015/libraryh3lp.unb.lib.css')
        )->toString(),
      ],
    ];

    $render_array_list = [
      '#title' => 'Ask Us',
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#context' => [
        'list_style' => 'inline',
      ],
      '#wrapper_attributes' => [
        'id' => 'askus-wrapper',
        'class' => [
          'askBubble',
        ],
      ],
      '#items' => $quicklinks,
    ];

    return $render_array_list;
  }

}
