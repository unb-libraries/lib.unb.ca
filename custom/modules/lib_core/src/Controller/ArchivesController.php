<?php

namespace Drupal\lib_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides route responses for the lib_core module.
 */
class ArchivesController extends ControllerBase {

  /**
   * Handle redirects from the old eloquent system, with an optional permalink ID.
   */
  public function eloquentRedirect(Request $request) {
    $url = '#';

    $id = $request->query->get('id');
    if (!empty($id)) {
      $id = str_replace('KEY_', '', $id);
      $url = "https://gencat.eloquent-systems.com/unb_permalink.html?key={$id}";
    }

    $build['info'] = [
      '#markup' => '<p>The Gateway Archives server has been migrated to a new platform powered by ArchivEra.</p>
<p>Unfortunately, the vendor is not able to map URLs or permalinks to the new system. As a result, any bookmarks or links to specific resources, collections or searches will need to be recreated on the new platform.</p>
<p><strong>Please update your bookmarks</strong>.</p>
<p>If you need assistance, please contact <a href="/contact-unb-libraries-staff?recipient=archives&amp;subject=Gateway%20Migration%20Question">archives@unb.ca</a>.</p>
<p><a class="btn btn-danger" href="' . $url . '">Connect to the new Gateway server</a></p>',
    ];

    return $build;
  }

}
