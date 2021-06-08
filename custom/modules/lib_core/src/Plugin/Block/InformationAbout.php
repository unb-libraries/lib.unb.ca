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
        <li><a href="#">Library Spaces</a></li>
        <li><a href="#">Citing Your Sources</a></li>
        <li><a href="https://guides.lib.unb.ca/guide/206">Zotero</a></li>
        <li><a href="/copyright">Copyright</a></li>
        <li><a href="/services/printing-scanning-photocopying">Printing, Scanning &amp; Photocopying</a></li>
        <li><a href="/rdm">Research Data Management</a></li>
        <li><a href="/gddm/data">Data Services</a></li>
        <li><a href="/gddm/govdocs">Government Documents</a></li>
        <li><a href="/gddm/maps">Maps &amp; GIS</a></li>
        <li><a href="/cds/publishing-services">Digital Publishing Services</a></li>
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
