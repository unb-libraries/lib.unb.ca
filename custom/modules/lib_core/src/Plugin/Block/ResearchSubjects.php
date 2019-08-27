<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Research Subjects' block for the UNB Libraries front page.
 *
 * @Block(
 *  id = "research_subjects",
 *  admin_label = @Translation("UNB Libraries Research Subjects"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class ResearchSubjects extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $categories = _lib_core_get_guide_categories();
    $col_list_item = 1;
    $col_list_items = ceil(count($categories) / 3);
    $last_item_label = end($categories);

    $html = '<h2>Research by Subject</h2>';
    $html .= '<div class="d-flex flex-wrap">';
    $last_list_item_html =
      '<li class="list-unstyled more-link mt-2">
        <span class="fa-plus-square fas"></span>&nbsp;<a
        href="//guides.lib.unb.ca/research-guides">All Research Guides</a>
      </li>
      </ul>';

    foreach ($categories as $value => $label) {
      if ($col_list_item == 1) {
        $html .= '<ul class="flex-grow-1 mb-0">';
      }

      $html .= '<li><a href="//guides.lib.unb.ca/category/' . $value . '">' . $label . '</a></li>';
      if ($col_list_item < $col_list_items) {
        if ($label === $last_item_label) {
          $html .= $last_list_item_html;
        }
        else {
          $col_list_item++;
        }
      }
      elseif ($label === $last_item_label) {
        $html .= $last_list_item_html;
      }
      else {
        $col_list_item = 1;
        $html .= '</ul>';
      }
    }
    $html .= '</div>';

    $render_array['wrapper'] = [
      '#type' => 'html_tag',
      '#tag' => 'section',
      '#attributes' => [
        'id' => [
          'research-subjects',
        ],
        'class' => [
          'mb-5',
          'mt-5',
          'p-0',
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
