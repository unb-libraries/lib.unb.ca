<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Today's Hours for all libraries block.
 *
 * @Block(
 *   id = "library_hours_today_block",
 *   admin_label = @Translation("Library Hours (Today)"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class LibraryHoursToday extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attached' => [
        'library' => [
          'lib_core/library-lib-hours',
        ],
      ],
      '#attributes' => [
        'class' => [
          'table-lib-hours',
        ],
      ],
      '#value' => $this->getTodaysHours(),
    ];
  }

  /**
   * Gets the Library Hours table structure.
   *
   * @return string
   *   The html structure for library hours (table).
   */
  protected function getTodaysHours() {
    $todays_hours_html = '
    <table class="m-0">
      <caption>
        <span class="sr-only">Library hours for</span>' .
        date("l, F j, Y") .
      '</caption>
      <tbody>
        <tr>
          <th scope="row"><a href="/about/harriet-irving-library">Harriet Irving</a></th>
          <td class="ch-nd ch-live ch-live-co ch-live-nsdo ch-live-am" data-ch-id="hil"
            data-ch-days="0" data-ch-format-time="h:mma" data-ch-format-date="dd">Unavailable</td>
        </tr>
        <tr>
          <th scope="row"><a href="/about/science-forestry-library">Science &amp; Forestry</a></th>
          <td class="ch-nd ch-live ch-live-co ch-live-nsdo ch-live-am" data-ch-id="sfl"
            data-ch-days="0" data-ch-format-time="h:mma" data-ch-format-date="dd">Unavailable</td>
        </tr>
        <tr>
          <th scope="row"><a href="/about/engineering-computer-science-library">Engineering &amp; CS</a></th>
          <td class="ch-nd ch-live ch-live-co ch-live-nsdo ch-live-am" data-ch-id="ecsl"
            data-ch-days="0" data-ch-format-time="h:mma" data-ch-format-date="dd">Unavailable</td>
        </tr>
        <tr>
          <th scope="row"><a href="/about/hans-w-klohn-commons">Saint John</a></th>
          <td class="ch-nd ch-live ch-live-co ch-live-nsdo ch-live-am" data-ch-id="hwkc"
            data-ch-days="0" data-ch-format-time="h:mma" data-ch-format-date="dd">Unavailable</td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td class="pt-2" colspan="2">
            <a href="/about/hours">
                Complete Hours<i class="fas fa-clock fa-sm ml-1" aria-hidden="true"></i>
            </a>
          </td>
        </tr>
      </tfoot>
    </table>';

    return $todays_hours_html;
  }

}
