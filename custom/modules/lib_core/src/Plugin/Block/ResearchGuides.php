<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Research Guides' block.
 *
 * @Block(
 *  id = "research_guides",
 *  admin_label = @Translation("UNB Libraries Research Guides (Block)"),
 *  category = @Translation("UNB Libraries"),
 * )
 */
class ResearchGuides extends BlockBase {

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

  /**
   * Gets the Research Guides form.
   *
   * @return string
   *   The html structure for Research Guides block/form/select/chosen.
   */
  protected function getResearchGuidesContainer() {
    $categories = _lib_core_get_guide_categories();
    $options = '<option value="" aria-disabled="true">Please choose a subject...</option>';
    foreach ($categories as $value => $label) {
      $options .= '<option value="' . $value . '">' . $label . '</option>';
    }

    return '
      <div id="research-guides" class="front-page-border p-4 w-100">
        <h3>Key Resources by Subject</h3>
        <form id="category-select" class="chosen-compact my-3">
          <div class="d-flex flex-column flex-md-row">
            <label class="sr-only" for="database-subjects">
                Search research guides by subject
            </label>
            <div class="input-group flex-fill mb-2 mr-0 mr-md-2">
              <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1"><i class="fas fa-database"></i></span>
             </div>
              <select id="database-subjects" class="custom-chosen-select form-control required" name="category" aria-required="true" required="required">' .
                $options .
              '</select>
            </div>
            <div class="mb-2">
              <button class="btn btn-dark form-control px-3" type="submit">GO</button>
            </div>
          </div>
        </form>
        <div class="mt-4 text-left">
          <a href="https://guides.lib.unb.ca/research-guides">
            All Research Guides
          </a>
        </div>
      </div>
    ';
  }

}
