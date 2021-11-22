<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Dean's Message block.
 *
 * @Block(
 *   id = "research_commons_advert_block",
 *   admin_label = @Translation("Harriet Irving Library Research Commons"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class ResearchCommonsAdvert extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => [],
      ],
      '#value' => $this->getValue(),
    ];
  }

  /**
   * Gets the Library Hours table structure.
   *
   * @return string
   *   The html structure for library hours (table).
   */
  protected function getValue() {
    // Note: image manually resized to match Content Lg Breakpoint image style.
    $html = '
      <a href="/researchcommons">
        <figure class="bg-light front-page-border mb-0">
            <img src="/modules/custom/lib_core/img/hil-research-commons.png"
              alt="The Harriet Irving Library Research Commons has transformed the third floor of the
                   Harriet Irving Library into a modern, interdisciplinary, research-driven learning environment
                   to further innovation, scholarship, and research at UNB.">
            <figcaption class="caption-bottom m-4 mt-0"><strong>Innovation - Collaboration - Impact</strong>
                <div class="btn-link h4 mt-1">New spaces, technologies and expertise to advance your research at UNB Libraries</div>
             </figcaption>
        </figure>
      </a>
    ';

    return $html;
  }

}
