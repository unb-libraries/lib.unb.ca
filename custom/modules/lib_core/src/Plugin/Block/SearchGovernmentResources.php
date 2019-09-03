<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a BS4-styled form for custom search: GDDM > Find Government Resources Using Google Custom Searches.
 *
 * @Block(
 *  id = "gddm_search_gov_resources",
 *  admin_label = @Translation("Government Documents Form: Search Government Resources"),
 *  category = @Translation("UNB Libraries"),
 * )
 */
class SearchGovernmentResources extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $html = '
      <p>For those government documents which are available online, try using Google Custom Search, as seen below
       (code courtesty of MacOdrum Library, Carleton University).
      </p>
      <form action="https://www.google.com/cse" class="alert alert-info pt-4 theme-dark" id="searchbox_007843865286850066037:3ajwn2jlweq">
        <div class="form-row">
          <div class="col-md-6 form-group">
		        <label class="sr-only" for="q">Google Custom Government Document Search</label>
		        <input class="form-control" id="q" name="q" title="search government resources" type="text">
		      </div>
		      <div class="col-md-4 form-group">
            <label class="sr-only" for="cx">Limit to a geographic area</label>
            <select class="form-control" id="cx" name="cx" title="limit to geographic area">
              <option selected="selected" value="007843865286850066037:3ajwn2jlweq">Canada</option>
              <option value="005831701059278509261:koybsui6dgu">Government of Canada Publications</option>
              <option value="007843865286850066037:4-bnftxu7fu">United States</option>
              <option value="007843865286850066037:b0heuatvay8">Intergovernmental Organizations</option>
            </select>
		      </div>
		      <div class="col-2">
		        <input class="btn btn-primary" name="sa" type="submit" value="GO">
		      </div>
		    </div>
		    <input name="siteurl" type="hidden" value="lib.unb.ca/gddm/govdocs">
		    <input name="ref" type="hidden" value="">
		    <input name="ss" type="hidden" value="">
		    <script type="text/javascript" src="https://www.google.com/coop/cse/brand?form=searchbox_007843865286850066037:3ajwn2jlweq"></script>
	    </form>
	    <p>Not sure you\'ve found everything using a search engine?  Try the following resources, or contact the 
	      <a href="/contact-unb-libraries-staff?recipient=govdocs">Government Documents staff</a> for assistance.
	    </p>';

    $render_array['form'] = [
      '#type' => 'markup',
      '#children' => $html,
    ];

    return $render_array;
  }

}
