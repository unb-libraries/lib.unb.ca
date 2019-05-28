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

    $html = _unb_libraries_askus_chat_widget('popup');

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
