<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Render\Markup;
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
          Url::fromUri('https://lib.unb.ca/services/docdel')
        )->toString(),
      ],
      [
        '#wrapper_attributes' => [
          'class' => [
            'list-inline-item',
          ],
        ],
        '#children' => Link::fromTextAndUrl(
          $this->t('Recalls'),
          Url::fromUri('https://lib.unb.ca/services/recalls')
        )->toString(),
      ],
      /*[
        '#wrapper_attributes' => [
          'class' => [
            'list-inline-item',
          ],
        ],
        '#children' => Link::fromTextAndUrl(
          $this->t('Group Study Rooms'),
            Url::fromUri('https://lib.unb.ca/services/group-study-rooms')
        )->toString(),
      ],*/
      [
        '#wrapper_attributes' => [
          'class' => [
            'list-inline-item',
          ],
        ],
        '#children' => Link::fromTextAndUrl(
          $this->t('Floor Plans'),
            Url::fromUri('https://lib.unb.ca/about/find-us')
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
      /*[
        '#wrapper_attributes' => [
          'class' => [
            'list-inline-item',
          ],
        ],
        '#children' => Link::fromTextAndUrl(
          $this->t('Borrow Tech & Tools'),
            Url::fromUri('https://lib.unb.ca/services/borrow-tech-tools')
        )->toString(),
      ],*/
      [
        '#wrapper_attributes' => [
          'class' => [
            'list-inline-item',
          ],
        ],
        '#children' => Link::fromTextAndUrl(
          Markup::create('<span class="fas fa-calendar-check mr-1"></span>' . $this->t('Book a Seat')),
          Url::fromUri('https://lib.unb.ca/services/bookings', [
            'attributes' => [
              'class' => [
                'btn',
                'btn-secondary',
                'btn-sm',
                'text-white',
              ],
            ],
          ])
        )->toString(),
      ],
    ];

    $attachments = [
      'library' => [
        'lib_core/quicklinks',
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
          'mb-2',
        ],
      ],
      '#items' => $quicklinks,
      '#attached' => $attachments,
    ];

    return $render_array_list;
  }

}
