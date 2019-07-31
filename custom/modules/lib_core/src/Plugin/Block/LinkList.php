<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Allows to place a list of links inside the sidebar.
 *
 * @Block(
 *   id = "link_list_block",
 *   admin_label = @Translation("Static Links"),
 *   category = @Translation("Lists"),
 * )
 */
class LinkList extends BlockBase {

  /**
   * {@inheritDoc}
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
    $request = \Drupal::request();
    foreach ($links as $url => $title) {
      $url_components = parse_url($url);
      if (isset($url_components['scheme']) && isset($url_components['host'])) {
        $absolute_url = $url;
      }
      else {
        $absolute_url = $request->getSchemeAndHttpHost() . $request->getRequestUri() . '/' . $url;
      }
      $li = [
        '#type' => 'html_tag',
        '#tag' => 'li',
      ];
      $li[] = [
        '#type' => 'link',
        '#title' => $title,
        '#url' => Url::fromUri($absolute_url),
      ];
      $build['list'][] = $li;
    }

    return $build;
  }

}
