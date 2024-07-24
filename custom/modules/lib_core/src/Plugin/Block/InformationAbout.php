<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a front page 'More information' 2-column link list.
 *
 * @Block(
 *  id = "information_about_block",
 *  admin_label = @Translation("Information About"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class InformationAbout extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $html = '
      <ul class="list-colcount-2">
        <li><a href="/copyright">Copyright</a></li>
        <li><a href="/gddm/data">Data Services</a></li>
        <li><a href="/gddm/govdocs">Government Documents</a></li>
        <li><a href="/about/policies">Library Policies</a></li>
        <li><a href="/gddm/maps">Maps &amp; GIS</a></li>
        <li><a href="/openaccess">Open Access</a></li>
        <li><a href="/rdm">Research Data Management</a></li>
        <li><a href="/services/writing-help">Writing & Citation Help</a></li>
        <li><a href="/guides/zotero">Zotero</a></li>
      </ul>
    ';

    $render_array['wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'info-about-wrapper',
        ],
      ],
    ];
    $render_array['wrapper']['children'] = [
      '#type' => 'markup',
      '#markup' => $html,
    ];

    return $render_array;
  }

}
