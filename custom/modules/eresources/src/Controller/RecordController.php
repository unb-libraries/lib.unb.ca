<?php

namespace Drupal\eresources\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\eresources\LocalResult;
use Drupal\search_api\Entity\Index;

/**
 * Provides route responses for the lib_core module.
 */
class RecordController extends ControllerBase {

  /**
   * Displays a permalink view of a record.
   *
   * @param int $id
   *   Record ID.
   *
   * @return array
   *   A simple renderable array.
   */
  public function permalink($id) {
    $index = Index::load('eresources');
    $indexQuery = $index->query();
    $indexQuery->addCondition('id', $id);
    $indexQuery->addCondition('status', TRUE);
    $indexQuery->addCondition('is_local', FALSE);
    $indexQuery->range(0, 1);
    $result = $indexQuery->execute();

    if ($result->getResultCount() == 0) {
      return [
        '#markup' => '<p class="mt-3"><span class="text-danger fas fa-exclamation-triangle"></span> It appears we no longer license this resource or you have followed an invalid resource number.</p><p>Try searching <a href="?form_id=eres_databases">Databases</a> or <a href="?form_id=eres_reference">e-Reference Materials</a> for alternatives.</p>',
      ];
    }

    $item = array_values($result->getResultItems())[0];
    $entry = new LocalResult($item);
    return [
      '#theme' => 'permalink',
      '#entry' => $entry,
      '#debug' => $this->currentUser()->hasPermission('administer eresources_record entities'),
      '#cache' => [
        'tags' => $item->getOriginalObject()->getValue()->getCacheTags(),
      ],
    ];
  }

  /**
   * Redirect for entity.eresources_record.collection.
   */
  public function collectionRedirect() {
    return $this->redirect('view.eresources_records.records');
  }

}
