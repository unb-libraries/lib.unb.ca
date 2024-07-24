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
          'class' => [],
        ],
        '#children' => Link::fromTextAndUrl(
          $this->t('Renew Books / My Account'),
          Url::fromUri('https://unb.on.worldcat.org/myaccount')
        )->toString(),
      ],
      [
        '#wrapper_attributes' => [
          'class' => [],
        ],
        '#children' => Link::fromTextAndUrl(
          $this->t('Document Delivery'),
          Url::fromUri('base://services/docdel')
        )->toString(),
      ],
      [
        '#wrapper_attributes' => [
          'class' => [],
        ],
        '#children' => Link::fromTextAndUrl(
          /* Markup::create('<span class="fas fa-undo-alt fa-sm mr-1"></span>' .
          $this->t('Recalls')),*/
          $this->t('Recalls'),
          Url::fromUri('base://services/recalls')
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
          'class' => [],
        ],
        '#children' => Link::fromTextAndUrl(
          $this->t('Floor Plans'),
          Url::fromUri('base://about/find-us')
        )->toString(),
      ],
      /*[
        '#wrapper_attributes' => [
          'class' => [],
        ],
        '#children' => Link::fromTextAndUrl(
          $this->t('Zotero'),
          Url::fromUri('https://lib.unb.ca/guides/zotero')
        )->toString(),
      ],*/
      [
        '#wrapper_attributes' => [
          'class' => [
            'list-inline-item',
          ],
        ],
        '#children' => Link::fromTextAndUrl(
          $this->t('Borrow Tech & Tools'),
            Url::fromUri('base://services/tech-tools')
        )->toString(),
      ],
    ];
    $misc_btn = [
      '#children' => Link::fromTextAndUrl(
        Markup::create('<span class="fas fa-star mr-1"></span>' . $this->t('Research Commons')),
        Url::fromUri('base://researchcommons', [
          'attributes' => [
            'id' => 'misc-btn',
            'class' => [
              'btn',
              'btn-irving',
              'text-nowrap',
            ],
          ],
        ])
      )->toString(),
    ];
    $booking_btn = [
      '#children' => Link::fromTextAndUrl(
        Markup::create('<span class="fas fa-calendar-check mr-1"></span>' . $this->t('Book a Study Space')),
        Url::fromUri('base://services/bookings', [
          'attributes' => [
            'class' => [
              'btn',
              'btn-dark',
              'text-nowrap',
            ],
            'id' => 'book-btn',
          ],
        ])
      )->toString(),
    ];
    $covid_btn_mobile = [
      '#children' => Link::fromTextAndUrl(
        Markup::create('<span class="fas fa-shield-virus mr-1"></span>' . $this->t('Covid-19')),
        Url::fromUri('base://what-to-expect', [
          'attributes' => [
            'class' => [
              'btn',
              'btn-unb-red',
              'd-lg-none',
              'text-nowrap',
            ],
            'id' => 'covid-btn-mobile',
          ],
        ])
      )->toString(),
    ];

    $attachments = [
      'library' => [
        'lib_core/quicklinks',
      ],
    ];

    $render_array_list = [
      '#type' => 'markup',
      '#prefix' => '<div id="quicklinks" class="d-flex mt-2 mb-4 mb-lg-5">',
      '#suffix' => '</div>',

      'quicklinks_list' => [
        '#title' => 'Quick Links:',
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
        '#attached' => $attachments,
      ],
      'quicklinks_buttons' => [
        '#theme' => 'item_list',
        '#list_type' => 'ul',
        '#attributes' => [
          'class' => [
            'd-flex',
            'flex-wrap',
            'justify-content-center',
            'list-inline',
          ],
        ],
        '#wrapper_attributes' => [
          'class' => [
            'ml-auto',
            'mr-auto',
            'mr-lg-0',
            'mt-2',
            'mt-lg-0',
          ],
          'id' => ['quicklinks-buttons'],
        ],
        '#items' => [
          'covid_button' => $misc_btn,
          'book_button' => $booking_btn,
        ],
      ],
    ];

    return $render_array_list;
  }

}
