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
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link" href="#circ">Access Services (Circulation &amp; Document Delivery)</a></li>
          <li class="nav-item"><a class="nav-link" href="#acquis">Acquisitions</a></li>
          <li class="nav-item"><a class="nav-link" href="#admin">Administration</a></li>
          <li class="nav-item"><a class="nav-link" href="#archives">Archives</a></li>
          <li class="nav-item"><a class="nav-link" href="#catalogue">Cataloguing</a></li>
          <li class="nav-item"><a class="nav-link" href="#cds">Centre for Digital Scholarship</a></li>
          <li class="nav-item"><a class="nav-link" href="#children">Children\'s Literature</a></li>
          <li class="nav-item"><a class="nav-link" href="#circ">Circulation (see Access Services)</a></li>
          <li class="nav-item"><a class="nav-link" href="#colldev">Collections Development</a></li>
          <li class="nav-item"><a class="nav-link" href="#docdel">Document Delivery (see Access Services)</a></li>
          <li class="nav-item"><a class="nav-link" href="#englib">Engineering &amp; Computer Science Library</a></li>
          <li class="nav-item"><a class="nav-link" href="#gddam">Government Documents, Data and Maps</a></li>
          <li class="nav-item"><a class="nav-link" href="#learning">Instruction Facilities</a></li>
          <li class="nav-item"><a class="nav-link" href="#lawlib">Law Library</a></li>
          <li class="nav-item"><a class="nav-link" href="#mailroom">Mail Room</a></li>
          <li class="nav-item"><a class="nav-link" href="#microforms">Microforms</a></li>
          <li class="nav-item"><a class="nav-link" href="#reference">Reference</a></li>
          <li class="nav-item"><a class="nav-link" href="#scilib">Science &amp; Forestry Library</a></li>
          <li class="nav-item"><a class="nav-link" href="#systems">Systems</a></li>
        </ul>
        <h2>Saint John:</h2>
        <ul class="nav">
          <li class="nav-item"><a class="nav-link" href="https://lib.unb.ca/about/staff.php#hwk">Hans W. Klohn Commons</a></li>
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
