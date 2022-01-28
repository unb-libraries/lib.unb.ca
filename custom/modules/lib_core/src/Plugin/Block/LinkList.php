<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
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

    $is_ordered = $config['is_ordered'] ?? FALSE;
    $build = [
      '#type' => 'container',
      'list' => [
        '#type' => 'html_tag',
        '#tag' => $is_ordered ? 'ol' : 'ul',
      ],
    ];

    $links = $config['links'] ?? [];
    foreach ($links as $uri => $title) {
      try {
        $url = Url::fromUri($uri);
      }
      catch (\InvalidArgumentException $e) {
        continue;
      }

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
        $css_external = $config['css_external'] ?? '';
        $classes[] = $css_external;

        if (!isset($config['external_target_blank']) || $config['external_target_blank']) {
          $a['#attributes']['target'] = '_blank';
        }
      }

      if ($this->isPdfUrl($url)) {
        $css_file = $config['css_file'] ?? '';
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

  /**
   * {@inheritDoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $is_ordered = $config['is_ordered'] ?? 0;
    $links = '';
    if (isset($config['links'])) {
      foreach ($config['links'] as $url => $title) {
        $links .= $url . '|' . $title . "\n";
      }
    }
    $form['links'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Links'),
      '#description' => $this->t('Enter a list of links, one link per line, e.g. "https://lib.unb.ca|UNB Libraries"'),
      '#rows' => 10,
      '#default_value' => $links,
    ];

    $form['is_ordered'] = [
      '#type' => 'select',
      '#title' => $this->t('Ordered list?'),
      '#options' => [
        0 => $this->t('No'),
        1 => $this->t('Yes'),
      ],
      '#default_value' => (int) $is_ordered,
    ];

    $open_external_in_new_tab = $config['external_target_blank'] ?? 1;
    $form['external_target_blank'] = [
      '#type' => 'select',
      '#title' => $this->t('Open external links in new tab?'),
      '#options' => [
        0 => $this->t('No'),
        1 => $this->t('Yes'),
      ],
      '#default_value' => (int) $open_external_in_new_tab,
    ];

    $css_external_class = $config['css_external'] ?? 'external';
    $form['css_external'] = [
      '#type' => 'textfield',
      '#title' => 'External Link CSS Class',
      '#default_value' => $css_external_class,
    ];

    $css_file_class = $config['css_file'] ?? 'file';
    $form['css_file'] = [
      '#type' => 'textfield',
      '#title' => 'PDF File Link CSS Class',
      '#default_value' => $css_file_class,
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);

    $links_form_value = trim($form_state->getValue('links'));
    foreach (explode("\n", $links_form_value) as $row) {
      $link = explode('|', $row);
      $links[trim($link[0])] = trim($link[1]);
    }

    $this->configuration['links'] = $links ?? [];
    $this->configuration['is_ordered'] = (bool) $form_state->getValue('is_ordered');
    $this->configuration['external_target_blank'] = (bool) $form_state->getValue('external_target_blank');
    $this->configuration['css_external'] = $form_state->getValue('css_external');
    $this->configuration['css_file'] = $form_state->getValue('css_file');
  }

}
