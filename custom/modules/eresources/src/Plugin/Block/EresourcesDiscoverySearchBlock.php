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
    $formBuilder = \Drupal::formBuilder();
    $renderer = \Drupal::service('renderer');
    $formId = \Drupal::request()->get('form_id');
    if (empty($formId)) {
      $formId = 'databases';
    }

    $forms = [
      'databases' => 'Article Databases',
      'journals' => 'Journals',
      'reference' => 'e-Reference Materials',
      'ebooks' => 'eBooks',
      'videos' => 'Videos',
    ];

    $build = '
     <div class="Accordion d-flex flex-column flex-lg-row">
        <div id="eresources-discovery-search" class="flex-grow-1">
        <div class="card border border-dark rounded-0">
          <div class="card-header bg-black px-2 pb-0 pb-md-1 rounded-0">
            <h2 class="sr-only">Search</h2>
            <nav class="navbar navbar-expand-md text-nowrap w-75">
              <h3 id="category-label" class="d-block d-md-none h4 px-2 py-1">Article Databases</h3>
              <button class="navbar-toggler text-white" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                Switch search
              </button>
              <div class="collapse mx-3 mx-lg-0 navbar-collapse" id="navbarNav">
                <ul class="navbar-nav d-flex align-items-lg-end justify-content-between w-100">
    ';

    foreach ($forms as $form => $title) {
      $expanded = 'false';
      $tabindex = ' tabindex="-1"';
      if (preg_match("/$form/", $formId)) {
        $expanded = 'true';
        $tabindex = '';
      }
      $build .= '<li class="nav-item"><button href="?form_id=eresources_' . $form . '_form" aria-controls="' . $form . '" aria-expanded="' . $expanded . '" class="Accordion-trigger p-2" id="' . $form . 'Btn"' . $tabindex . '>' . $title . '</button></li>';
    }

    $build .= '
                </ul>
              </div>
            </nav>
          </div>
          <div class="card-body bg-light px-2 py-0">
    ';

    foreach ($forms as $form => $title) {
      $hidden = preg_match("/$form/", $formId) ? '' : 'hidden';
      $formRender = $formBuilder->getForm('Drupal\eresources\Form\\' . ucfirst($form) . 'Form');
      $build .= '<div aria-labelledby="' . $form . 'Btn" class="Accordion-panel" id="' . $form . '" role="region"' . $hidden . '>' . $renderer->render($formRender) . '</div>';
    }

    $build .= '
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
          'eresources/eresources',
        ],
      ],
    ];
  }

}
