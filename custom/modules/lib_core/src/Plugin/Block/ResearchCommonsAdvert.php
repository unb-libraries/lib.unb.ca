<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Research Commons feature block.
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
        'class' => [
          'news-featured',
        ],
      ],
      '#value' => $this->getValue(),
    ];
  }

  /**
   * Gets the HTML to be included inside the Research Commons block wrapper.
   *
   * @return string
   *   The HTML markup of the Research commons feature.
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
            <figcaption class="caption-bottom m-4 mt-0">
                <p class="btn-link h3">Innovation - Collaboration - Impact</p>
                <p class="h5">New spaces, technologies and expertise to advance your research at UNB Libraries</p>
             </figcaption>
        </figure>
      </a>
    ';

    return $html;
  }

}
