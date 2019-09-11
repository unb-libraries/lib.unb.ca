<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a BS4-styled form for Faculty Support > Permitted Use.
 *
 * @Block(
 *  id = "permitted_use_tool",
 *  admin_label = @Translation("Permitted Use Tool - DB/Resource"),
 *  category = @Translation("UNB Libraries"),
 * )
 */
class PermittedUseTool extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $html = '
      <form action="//web.lib.unb.ca/eresources/index.php" class="alert alert-warning mx-4 my-2 theme-dark" method="get" _lpchecked="1">
        <div class="form-row mb-2">
          <input id="sub" name="sub" type="hidden" value="all">
		      <input name="searchtype" type="hidden" value="every_word">
		      <b class="text-dark">Check Permitted Use of Resource</b>
		    </div>
		    <div class="form-row mx-2">
          <div class="form-group mb-0">
            <label class="font-weight-bold mx-1 my-0 py-2" for="title">Resource Name</label>
          </div>
          <div class="col-lg-5 form-group">
            <input class="form-control" id="title" name="title" placeholder="enter database or journal name" type="text">
            <p class="mt-2">
              <i aria-hidden="true" class="fas fa-gavel fa-flip-horizontal"></i>
              <a href="//web.lib.unb.ca/eresources/permitteduse.php">Understanding the permitted use tool</a>
            </p>
          </div>
          <div class="col form-group">
            <input class="btn btn-primary" type="submit" value="Check Permitted Use">
          </div>
        </div>
      </form>';

    $render_array['form'] = [
      '#type' => 'markup',
      '#children' => $html,
    ];

    return $render_array;
  }

}
