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
      <form action="/eresources" class="alert alert-warning my-3 theme-dark" method="get">
        <input type="hidden" name="type" value="title">
        <div class="form-row mb-2">
          <b class="text-dark">Check Permitted Use of Resource</b>
	</div>
	<div class="form-row mx-2">
          <div class="form-group mb-0">
            <label class="font-weight-bold mx-1 my-0 py-2" for="title">Resource Name</label>
          </div>
          <div class="col-lg-5 form-group">
            <input class="form-control" id="title" name="query" placeholder="enter resource name" type="text">
            <div class="custom-control-wrapper-inline radio text-black mt-2">
              <div class="custom-control custom-radio">
                <input data-drupal-selector="search-type-title" type="radio" id="search-type-title" name="form_id" value="eres_databases" checked="checked" class="form-radio custom-control-input">
                <label class="custom-control-label" for="search-type-database">Database</label>
              </div>
              <div class="custom-control custom-radio">
                <input data-drupal-selector="search-type-journal" type="radio" id="search-type-journal" name="form_id" value="eres_journals" class="form-radio custom-control-input">
                <label class="custom-control-label" for="search-type-journal">Journal/Newspaper</label>
              </div>
              <div class="custom-control custom-radio">
                <input data-drupal-selector="search-type-ereference" type="radio" id="search-type-ereference" name="form_id" value="eres_reference" class="form-radio custom-control-input">
                <label class="custom-control-label" for="search-type-ereference">e-Reference Material</label>
              </div>
            </div>
            <div class="d-flex flex-row mt-2">
              <div class="mr-2"><i aria-hidden="true" class="fas fa-gavel fa-flip-horizontal"></i></div>
              <div><a href="/eresources/permitted-use-licensed-content">Understanding the permitted use tool</a></div>
            </div>
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
