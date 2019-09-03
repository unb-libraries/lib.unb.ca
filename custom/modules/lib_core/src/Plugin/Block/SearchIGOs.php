<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a BS4-styled form for custom search: GDDM > Search IGOs.
 *
 * @Block(
 *  id = "gddm_search_igos",
 *  admin_label = @Translation("Government Documents Form: Search IGOs"),
 *  category = @Translation("UNB Libraries"),
 * )
 */
class SearchIGOs extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $html = '
      <form action="https://www.google.com/cse" class="alert alert-info mb-4 pt-4 theme-dark" id="searchbox_006748068166572874491:55ez0c3j3ey">
        <div class="form-row">
          <input name="cx" type="hidden" value="006748068166572874491:55ez0c3j3ey">
          <div class="col-md-10 form-group">
            <label class="sr-only" for="q">Google Custom Government Document Search</label>
            <input class="form-control" name="q" type="text">
          </div>
          <div class="col-2">  
            <input class="btn btn-primary" name="sa" type="submit" value="GO">
          </div>
          <input name="cof" type="hidden" value="FORID:0">
        </div>
        <script src="https://www.google.com/coop/cse/brand?form=searchbox_006748068166572874491:55ez0c3j3ey" type="text/javascript"></script>
      </form>';

    $render_array['form'] = [
      '#type' => 'markup',
      '#children' => $html,
    ];

    return $render_array;
  }

}
