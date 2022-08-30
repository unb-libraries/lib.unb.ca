<?php

namespace Drupal\guides\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides route responses for the guides module.
 */
class GuidesController extends ControllerBase {

  /**
   * Redirect old guide ID to new ID.
   *
   * @param int $id
   *   Old Guide ID.
   */
  public function legacyRedirect($id) {
    $storage = $this->entityTypeManager()->getStorage('guide');
    $query = $storage->getQuery();
    $ids = $query
      ->condition('status', 1)
      ->condition('old_guide_id', $id)
      ->execute();

    if (empty($ids)) {
      throw new NotFoundHttpException();
    }

    return new RedirectResponse(Url::fromRoute('entity.guide.canonical', ['guide' => reset($ids)])->toString());
  }

  /**
   * List of categories for main guides page.
   */
  public function categories() {
    $storage = $this->entityTypeManager()->getStorage('guide_category');
    $query = $storage->getQuery();
    $ids = $query
      ->condition('status', 1)
      ->sort('title', 'ASC')
      ->execute();
    $list = $storage->loadMultiple($ids);

    return [
      '#theme' => 'categories',
      '#title' => 'Research by Subject',
      '#categories' => $list,
      '#cache' => [
        'tags' => [
          'guide_category_list',
        ],
      ],
    ];
  }

}
