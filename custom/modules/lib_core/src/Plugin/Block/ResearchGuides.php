<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Research Guides' block.
 *
 * @Block(
 *  id = "research_guides",
 *  admin_label = @Translation("UNB Libraries Research Guides"),
 *  category = @Translation("UNB Libraries"),
 * )
 */
class ResearchGuides extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#children' => $this->getResearchGuidesContainer(),
      '#attached' => [
        'library' => [
          'lib_core/lib-chosen',
          'lib_core/chosen-bootstrap',
          'lib_core/research-guides',
        ],
      ],
    ];
  }

  /**s
   * Gets the Research Guides form.
   *
   * @return string
   *   The html structure for Research Guides block/form/select/chosen.
   */
  protected function getResearchGuidesContainer() {
    $categories = _lib_core_get_guide_categories();
    $options = '<option value="">* Please choose an option</option>';
    foreach ($categories as $value => $label) {
      $options .= '<option value="' . $value .'">'. $label . '</option>';
    }

    return '
      <div id="research-guides">
        <h3>Key Resources by Subject</h3>
        <form id="category-select" class="chosen-compact my-3">
          <div class="d-flex flex-column flex-lg-row">
            <label class="sr-only" for="database-subjects">
                Search research guides by subject
            </label>
            <div class="input-group flex-fill mb-2 mr-0 mr-lg-2">
              <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1"><i class="fas fa-database"></i></span>
             </div>
              <select id="database-subjects" class="custom-chosen-select form-control required" name="category" required>' .
                $options .
              '</select>
            </div>
            <div class="mb-2">
              <button class="btn btn-dark form-control px-3" type="submit">GO</button>
            </div>
          </div>
        </form>
        <div class="text-right">
          <a href="https://guides.lib.unb.ca/research-guides">All Research Guides
            <i class="fas fa-external-link-alt fa-xs fa-text-top" aria-hidden="true"></i>
          </a>
        </div>
      </div>
    ';
  }
}
