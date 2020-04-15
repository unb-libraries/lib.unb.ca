<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Staff and Departments navigation block (for sidebar).
 *
 * @Block(
 *  id = "staff_directory_nav",
 *  admin_label = @Translation("Staff and Departments Sidebar Navigation"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class StaffDirectoryNav extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $html = '
        <h2>Fredericton:</h2>
        <ul class="list-unstyled">
          <li><a href="#circ">Access Services (Circulation &amp; Document Delivery)</a></li>
          <li><a href="#acquis">Acquisitions</a></li>
          <li><a href="#admin">Administration</a></li>
          <li><a href="#archives">Archives</a></li>
          <li><a href="#catalogue">Cataloguing</a></li>
          <li><a href="#cds">Centre for Digital Scholarship</a></li>
          <li><a href="#children">Children\'s Literature</a></li>
          <li><a href="#circ">Circulation (see Access Services)</a></li>
          <li><a href="#colldev">Collections Development</a></li>
          <li><a href="#circ">Document Delivery (see Access Services)</a></li>
          <li><a href="#englib">Engineering &amp; Computer Science Library</a></li>
          <li><a href="#gddm">Government Documents, Data and Maps</a></li>
          <li><a href="#learning">Instruction Facilities</a></li>
          <li><a href="//www.unb.ca/fredericton/law/library/about/directory.html">Law Library</a></li>
          <li><a href="#mailroom">Mail Room</a></li>
          <li><a href="#microforms">Microforms</a></li>
          <li><a href="#reference">Reference</a></li>
          <li><a href="#researchcommons">Research Commons</a></li>
          <li><a href="#scilib">Science &amp; Forestry Library</a></li>
          <li><a href="#systems">Systems</a></li>
        </ul>
        <h2 class="mt-4">Saint John:</h2>
        <ul class="list-unstyled">
          <li><a href="#hwk">Hans W. Klohn Commons</a></li>
        </ul>
        <p>
          <a href="https://phonebook.unb.ca/"><i class="fas fa-book-open"></i> UNB Phone Book</a>
        </p>';

    $render_array['wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'sticky-sidebar',
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
