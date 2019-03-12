<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides the UNB Libraries Front page QuickLinks Block.
 *
 * @Block(
 *   id = "quicklinks_block",
 *   admin_label = @Translation("UNB Libraries QuickLinks"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class QuickLinks extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $quicklinks = [
      [
        '#wrapper_attributes' => [
          'class' => [
            'list-inline-item',
          ],
        ],
        '#children' => Link::fromTextAndUrl(
            $this->t('Renew Books'),
            Url::fromUri('https://unb.on.worldcat.org/myaccount')
        )->toString(),
      ],
      [
        '#wrapper_attributes' => [
          'class' => [
            'list-inline-item',
          ],
        ],
        '#children' => Link::fromTextAndUrl(
          $this->t('Document Delivery'),
          Url::fromUri('https://lib.unb.ca/requests/docdel')
        )->toString(),
      ],
      [
        '#wrapper_attributes' => [
          'class' => [
            'list-inline-item',
          ],
        ],
        '#children' => Link::fromTextAndUrl(
          $this->t('Group Study Rooms'),
            Url::fromUri('https://lib.unb.ca/services/group_study.php')
        )->toString(),
      ],
      [
        '#wrapper_attributes' => [
          'class' => [
            'list-inline-item',
          ],
        ],
        '#children' => Link::fromTextAndUrl(
          $this->t('Floor Plans'),
            Url::fromUri('https://lib.unb.ca/about/findus.php')
        )->toString(),
      ],
      [
        '#wrapper_attributes' => [
          'class' => [
            'list-inline-item',
          ],
        ],
        '#children' => Link::fromTextAndUrl(
          $this->t('Zotero'),
            Url::fromUri('https://guides.lib.unb.ca/guide/206')
        )->toString(),
      ],
    ];

    $render_array_list = [
      '#title' => 'QuickLinks:',
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#context' => [
        'list_style' => 'inline',
      ],
      '#wrapper_attributes' => [
        'id' => 'quicklinks-wrapper',
        'class' => [
          'collapse',
        ],
      ],
      '#items' => $quicklinks,
    ];

    return $render_array_list;
  }

}
