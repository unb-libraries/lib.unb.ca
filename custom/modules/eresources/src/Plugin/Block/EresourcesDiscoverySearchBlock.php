<?php

namespace Drupal\eresources\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\eresources\Controller\RecordController;
use Drupal\eresources\Form\TrialsForm;

/**
 * Provides the UNB Libraries eResources Discovery Search Block.
 *
 * @Block(
 *   id = "eresources_discovery_search_block",
 *   admin_label = @Translation("UNB Libraries eResources Discovery Search"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class EresourcesDiscoverySearchBlock extends BlockBase {

  /**
   * Forms for display.
   *
   * @var array
   */
  private static $forms = [
    'databases',
    'journals',
    'reference',
    'ebooks',
    'videos',
  ];

  /**
   * {@inheritdoc}
   */
  public function build() {
    $formList = self::$forms;
    $formBuilder = \Drupal::formBuilder();
    $renderer = \Drupal::service('renderer');
    $formId = \Drupal::request()->get('form_id');
    $permalink = \Drupal::request()->get('id');

    $trials = FALSE;
    $trialsEntries = TrialsForm::getTrials();
    if (!empty($trialsEntries)) {
      $trials = TRUE;
    }
    if ($trials || $formId == 'eres_trials') {
      $formList[] = 'trials';
    }

    $title = 'Resource';
    if (preg_match('/^eres_(.+)$/', $formId, $matches)) {
      $formId = $matches[1];
    }
    if (!$permalink && !in_array($formId, $formList)) {
      $formId = $formList[0];
    }
    if ($formId) {
      $formClass = '\Drupal\eresources\Form\\' . ucfirst($formId) . 'Form';
      $title = $formClass::getTitle();
    }

    $build = '
     <div class="Accordion d-flex flex-column flex-lg-row">
        <div id="eresources-discovery-search" class="flex-grow-1">
        <div class="card">
          <div class="card-header bg-black px-2 pb-0 pb-md-1 rounded-0">
            <h2 class="sr-only">Search</h2>
            <nav class="navbar navbar-expand-md text-nowrap">
              <h3 id="category-label" class="d-block d-md-none h4 px-2 py-1">' . $title . '</h3>
              <button class="navbar-toggler text-white" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                Switch search
              </button>
              <div class="collapse mx-3 mx-md-0 navbar-collapse" id="navbarNav">
                <ul class="navbar-nav d-flex align-items-lg-end justify-content-between w-100">
    ';

    foreach ($formList as $form) {
      $expanded = 'false';
      $tabindex = ' tabindex="-1"';
      if ($form == $formId) {
        $expanded = 'true';
        $tabindex = '';
      }
      $formClass = '\Drupal\eresources\Form\\' . ucfirst($form) . 'Form';
      $title = $formClass::getTitle();

      $build .= '<li class="nav-item"><button href="?form_id=eres_' . $form . '" aria-controls="' . $form . '" aria-expanded="' . $expanded . '" class="Accordion-trigger p-2" id="' . $form . 'Btn"' . $tabindex . '>' . $title . '</button></li>';
    }

    if ($permalink) {
      $build .= '<li class="nav-item"><button href="?id=' . urlencode($permalink) . '" aria-controls="resource" aria-expanded="true" class="Accordion-trigger p-2" id="resourceBtn">Resource</button></li>';
    }

    $build .= '
                </ul>
              </div>
            </nav>
          </div>
          <div class="card-body bg-light border border-black px-2 py-0">
    ';

    foreach ($formList as $form) {
      $hidden = ($form == $formId) ? '' : 'hidden';
      $formRender = $formBuilder->getForm('Drupal\eresources\Form\\' . ucfirst($form) . 'Form');
      unset($formRender['form_build_id']);
      $build .= '<div aria-labelledby="' . $form . 'Btn" class="Accordion-panel" id="' . $form . '" role="region"' . $hidden . '>' . $renderer->render($formRender) . '</div>';
    }

    if ($permalink) {
      $record = new RecordController();
      $resourceRender = $record->permalink($permalink);
      $build .= '<div aria-labelledby="resourceBtn" class="Accordion-panel" id="resource" role="region">' . $renderer->render($resourceRender) . '</div>';
    }

    $blockManager = \Drupal::service('plugin.manager.block');
    $askUsBlock = $blockManager->createInstance('askus_popup', []);
    $access = $askUsBlock->access(\Drupal::currentUser());
    if (is_object($access) && $access->isForbidden() || is_bool($access) && !$access) {
      $askUs = '';
    }
    else {
      $renderArray = $askUsBlock->build();
      $askUs = $renderer->render($renderArray);
    }

    $build .= '
          </div>
        </div>
        </div>
    </div>
    <div id="eresources-notices" class="row mt-3">
      <div class="col">
        <dl class="ml-3">
          <dt><span class="fas fa-bug"></span> Report a Problem</dt>
          <dd><a href="/help/ticket/new">Submit a Trouble Ticket</a> for technical problems accessing or using e-Resources.</dd>
          <dt><span class="fas fa-gavel"></span> Copyright Restrictions</dt>
          <dd>Resources are licensed to the University of New Brunswick for academic purposes ONLY. The content may not be reproduced, retransmitted, disseminated, sold, distributed, published, broadcast or circulated.  Remote access restricted to members of the University of New Brunswick/St. Thomas University community.</dd>
        </dl>
      </div>
      <div class="col mx-3">' . $askUs . '
      </div>
    </div>
    ';

    return [
      '#children' => $build,
      '#attached' => [
        'library' => [
          'lib_core/lib-selectize',
          'lib_core/accessible-accordion',
          'eresources/eresources',
        ],
      ],
    ];
  }

}
