<?php

namespace Drupal\eresources\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides the UNB Libraries eResources Discovery Search Block.
 *
 * @Block(
 *   id = "eresources_discovery_search_block",
 *   admin_label = @Translation("UNB Libraries eResources Discovery Search"),
 *   category = @Translation("UNB Libraries WIP"),
 * )
 */
class EresourcesDiscoverySearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = '
     <div class="Accordion d-flex flex-column flex-lg-row">
        <div id="eresources-discovery-search" class="flex-grow-1">
        <div class="card">
          <div class="card-header bg-black px-2 pb-0 pb-md-1">
            <h2 class="sr-only">Search</h2>
            <nav class="navbar navbar-expand-md text-nowrap">
              <h3 id="category-label" class="d-block d-md-none h4 px-2 py-1">Article Databases</h3>
              <button class="navbar-toggler text-white" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                Switch search
              </button>
              <div class="collapse mx-3 mx-lg-0 navbar-collapse" id="navbarNav">
                <ul class="navbar-nav d-flex align-items-lg-end justify-content-between w-100">
                  <li class="nav-item"><button aria-controls="indexes" aria-expanded="true" class="Accordion-trigger p-2" id="indexesBtn">Article Databases</button></li>
                  <li class="nav-item"><button aria-controls="journals" aria-expanded="false" class="Accordion-trigger p-2" id="journalsBtn" tabindex="-1">Journals</button></li>
                  <li class="nav-item"><button aria-controls="refmat" aria-expanded="false" class="Accordion-trigger p-2" id="refmatBtn" tabindex="-1">e-Reference Materials</button></li>
                  <li class="nav-item"><button aria-controls="ebooks" aria-expanded="false" class="Accordion-trigger p-2" id="ebooksBtn" tabindex="-1">eBooks</button></li>
                  <li class="nav-item"><button aria-controls="video" aria-expanded="false" class="Accordion-trigger p-2" id="videoBtn" tabindex="-1">Videos</button></li>
                  </li>
                </ul>
              </div>
            </nav>
          </div>
          <div class="card-body bg-light px-2 py-0">
            <div aria-labelledby="indexesBtn dropdownBtn1" class="Accordion-panel" id="indexes" role="region">' . $this->getBlockMarkup('databases_search_block') . '</div>
            <div aria-labelledby="journalsBtn dropdownBtn2" class="Accordion-panel" id="journals" role="region" hidden="">' . $this->getBlockMarkup('journals_search_block') . '</div>
            <div aria-labelledby="refmatBtn dropdownBtn3" class="Accordion-panel" id="refmat" role="region" hidden="">' . $this->getBlockMarkup('reference_search_block') . '</div>
            <div aria-labelledby="ebooksBtn dropdownBtn4" class="Accordion-panel" id="ebooks" role="region" hidden="">' . $this->getBlockMarkup('ebooks_search_block') . '</div>
            <div aria-labelledby="videoBtn dropdownBtn5" class="Accordion-panel" id="video" role="region" hidden="">' . $this->getBlockMarkup('videos_search_block') . '</div>
          </div>
        </div>
        </div>
    </div>
    ';

    return [
      '#children' => $build,
      '#attached' => [
        'library' => [
          'lib_core/accessible-accordion',
        ],
      ],
    ];
  }

  /**
   * Get HTML markup for a block plugin, given its plugin id.
   *
   * @param string $plugin_id
   *   The plugin id of the desired block.
   * @param array $configuration
   *   The configuration array for the desired block plugin, default NULL.
   *
   * @return \Drupal\Core\Render\Markup
   *   The rendered HTML markup for the block plugin ('broken/missing' message if invalid).
   */
  protected function getBlockMarkup($plugin_id, array $configuration = []) {
    $block_manager = \Drupal::service('plugin.manager.block');
    $plugin_block = $block_manager->createInstance($plugin_id, $configuration);
    $render = $plugin_block->build();

    return render($render);
  }

}
