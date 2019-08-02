<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Allows to place a list of links inside the sidebar.
 *
 * @Block(
 *   id = "link_list_block",
 *   admin_label = @Translation("Link List"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class LinkList extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    $is_ordered = isset($config['is_ordered']) ? $config['is_ordered'] : FALSE;
    $build = [
      '#type' => 'container',
      'list' => [
        '#type' => 'html_tag',
        '#tag' => $is_ordered ? 'ol' : 'ul',
      ],
    ];

    $links = isset($config['links']) ? $config['links'] : [];
    foreach ($links as $uri => $title) {
      $url = Url::fromUri($uri);
      $li = [
        '#type' => 'html_tag',
        '#tag' => 'li',
      ];

      $a = [
        '#type' => 'link',
        '#title' => $title,
        '#attributes' => [
          'class' => [],
        ],
        '#url' => $url,
      ];

      $classes = [];
      if ($url->isExternal()) {
        $css_external = isset($config['css_external']) ? $config['css_external'] : '';
        $classes[] = $css_external;

        if (!isset($config['external_target_blank']) || $config['external_target_blank']) {
          $a['#attributes']['target'] = '_blank';
        }
      }

      if ($this->isPdfUrl($url)) {
        $css_file = isset($config['css_file']) ? $config['css_file'] : '';
        $classes[] = $css_file;
      }

      $a['#attributes']['class'] = $classes;

      $li[] = $a;
      $build['list'][] = $li;
    }

    return $build;
  }

  /**
   * Determine whether the given URL links to a PDF file.
   *
   * @param \Drupal\Core\Url $url
   *   The URL to verify.
   *
   * @return bool
   *   True if the given URL links to a PDF file. False otherwise.
   */
  protected function isPdfUrl(Url $url) {
    $file_name_chunks = explode('.', $url->getUri());
    $extension = end($file_name_chunks);
    return $extension === 'pdf';
  }

}
