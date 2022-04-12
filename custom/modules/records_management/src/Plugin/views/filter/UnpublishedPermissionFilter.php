<?php

namespace Drupal\records_management\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\BooleanOperator;

/**
 * Filter a view by publication state and according permission(s).
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("unpublished_permission_filter")
 */
class UnpublishedPermissionFilter extends BooleanOperator {

  /**
   * {@inheritDoc}
   *
   * Refine the query to filter out unpublished entities unless the user has
   * permission to list them.
   *
   * @throws \Exception
   */
  public function query() {
    if (!$this->view->getUser()->hasPermission('list unpublished schedule entities')) {
      parent::query();
    }
  }

}
