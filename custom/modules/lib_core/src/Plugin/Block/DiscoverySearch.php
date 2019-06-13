<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides the UNB Libraries Discovery Search Home Block.
 *
 * @Block(
 *   id = "discovery_search_block",
 *   admin_label = @Translation("UNB Libraries Discovery Search"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class DiscoverySearch extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Load askus block plugin. See https://drupal.stackexchange.com/a/171733.
    $block_manager = \Drupal::service('plugin.manager.block');
    $config = [];
    $plugin_block = $block_manager->createInstance('askus_popup', $config);

    $access_result = $plugin_block->access(\Drupal::currentUser());
    // Return empty render array if user doesn't have access.
    // $access_result can be boolean or an AccessResult class.
    if (is_object($access_result) && $access_result->isForbidden() || is_bool($access_result) && !$access_result) {
      $askus_block = [];
    }
    else {
      // Store block render array in variable prior pass to render().
      $render_array = $plugin_block->build();

      $askus_block = '<div class="d-none d-lg-flex flex-shrink-1 py-4">';
      $askus_block .= render($render_array);
      $askus_block .= '</div>';
    }

    $html =
      '<div id="discovery-search" class="tabs-theme-dark">
        <ul id="tabs" class="flex-fill nav nav-tabs justify-content-center" role="tablist">
          <li class="nav-item">
            <a id="tab-reserves" href="#pane-reserves" class="nav-link active" data-toggle="tab" role="tab">
              Reserves
            </a>
          </li>
          <li class="nav-item">
            <a id="tab-catalogue" href="#pane-catalogue" class="nav-link" data-toggle="tab" role="tab">
              Catalogue
            </a>
          </li>
          <li class="nav-item">
            <a id="tab-databases" href="#pane-databases" class="nav-link" data-toggle="tab" role="tab">
               <span class="d-none d-xl-inline">Article</span> Databases
            </a>
          </li>
          <li class="nav-item">
            <a id="tab-journals" href="#pane-journals" class="nav-link" data-toggle="tab" role="tab">
              Journals<span class="d-none d-xl-inline"> &amp; Newspapers</span>
            </a>
          </li>
          <li class="nav-item">
            <a id="tab-encyclopedias" href="#pane-encyclopedias" class="nav-link" data-toggle="tab" role="tab">
              e-Encyclopedias<span class="d-none d-xl-inline">, etc.</span>
            </a>
          </li>
          <li class="nav-item">
            <a id="tab-ebooks" href="#pane-ebooks" class="nav-link" data-toggle="tab" role="tab">
              e-Books
            </a>
          </li>
          <li class="nav-item">
            <a id="tab-videos" href="#pane-videos" class="nav-link" data-toggle="tab" role="tab">
              Videos
            </a>
          </li>
        </ul>
        <div class="d-flex flex-wrap p-lg-4">
          <div id="content" class="flex-grow-1 tab-content" role="tablist">
            <div id="pane-reserves" class="card tab-pane fade show active" role="tabpanel" aria-labelledby="tab-reserves">
              <div class="card-header p-0" role="tab" id="heading-reserves">
                <h3 class="m-0">
                  <a class="nav-link mx-1 p-2" data-toggle="collapse" href="#collapse-reserves" aria-expanded="true" aria-controls="collapse-reserves">
                    Reserves
                  </a>
                </h3>
              </div>
              <div id="collapse-reserves" class="collapse show" data-parent="#content" role="tabpanel" aria-labelledby="heading-reserves">
                <div class="card-body">';
                  $html .= $this->getReservesForm();
                  $html .=
                '</div>
              </div>
            </div>

            <div id="pane-catalogue" class="card tab-pane fade" role="tabpanel" aria-labelledby="tab-catalogue">
              <div class="card-header p-0" role="tab" id="heading-catalogue">
                <h3 class="m-0">
                  <a class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-catalogue" aria-expanded="false" aria-controls="collapse-catalogue">
                    Catalogue
                  </a>
                </h3>
              </div>
              <div id="collapse-catalogue" class="collapse" data-parent="#content" role="tabpanel" aria-labelledby="heading-catalogue">
                <div class="card-body">';
                  $html .= $this->getCatalogueForm();
                  $html .=
                '</div>
              </div>
            </div>

            <div id="pane-databases" class="card tab-pane fade" role="tabpanel" aria-labelledby="tab-databases">
              <div class="card-header p-0" role="tab" id="heading-databases">
                <h3 class="m-0">
                  <a class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-databases" aria-expanded="false" aria-controls="collapse-databases">
                    <span class="d-none d-sm-inline">Article</span> Databases
                  </a>
                </h3>
              </div>
              <div id="collapse-databases" class="collapse" role="tabpanel" data-parent="#content" aria-labelledby="heading-databases">
                <div class="card-body">';
                  $html .= $this->getDatabasesForm();
                  $html .=
                '</div>
              </div>
            </div>
            
            <div id="pane-journals" class="card tab-pane fade" role="tabpanel" aria-labelledby="tab-journals">
              <div class="card-header p-0" role="tab" id="heading-journals">
                <h3 class="m-0">
                  <a class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-journals" aria-expanded="false" aria-controls="collapse-journals">
                    Journals<span class="d-none d-sm-inline"> &amp; Newspapers</span>
                  </a>
                </h3>
              </div>
                <div id="collapse-journals" class="collapse" role="tabpanel" data-parent="#content" aria-labelledby="heading-journals">
                  <div class="card-body">';
                    $html .= $this->getJournalsForm();
                    $html .=
                  '</div>
                </div>
            </div>

            <div id="pane-encyclopedias" class="card tab-pane fade" role="tabpanel" aria-labelledby="tab-encyclopedias">
              <div class="card-header p-0" role="tab" id="heading-encyclopedias">
                <h3 class="m-0">
                  <a class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-encyclopedias" aria-expanded="false" aria-controls="collapse-encyclopedias">
                    e-Encyclopedias<span class="d-none d-sm-inline">, etc.</span>
                  </a>
                </h3>
              </div>
                <div id="collapse-encyclopedias" class="collapse" role="tabpanel" data-parent="#content" aria-labelledby="heading-encyclopedias">
                  <div class="card-body">';
                    $html .= $this->getEncyclopediasForm();
                    $html .=
                  '</div>
                </div>
            </div>

            <div id="pane-ebooks" class="card tab-pane fade" role="tabpanel" aria-labelledby="tab-ebooks">
              <div class="card-header p-0" role="tab" id="heading-ebooks">
                <h3 class="m-0">
                  <a class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-ebooks" aria-expanded="false" aria-controls="collapse-ebooks">
                    e-Books
                  </a>
                </h3>
              </div>
              <div id="collapse-ebooks" class="collapse" role="tabpanel" data-parent="#content" aria-labelledby="heading-ebooks">
                <div class="card-body">';
                  $html .= $this->getEbooksForm();
                  $html .=
                '</div>
              </div>
            </div>

            <div id="pane-videos" class="card tab-pane fade" role="tabpanel" aria-labelledby="tab-videos">
              <div class="card-header p-0" role="tab" id="heading-videos">
                <h3 class="m-0">
                  <a class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-videos" aria-expanded="false" aria-controls="collapse-videos">
                    Videos
                  </a>
                </h3>
              </div>
              <div id="collapse-videos" class="collapse" role="tabpanel" data-parent="#content" aria-labelledby="heading-videos">
                <div class="card-body">';
                  $html .= $this->getVideosForm();
                  $html .=
                '</div>
              </div>
            </div>
          </div>';
          $html .= $askus_block;
          $html .=
        '</div>
      </div>
    </div>';

    return [
      '#children' => $html,
      '#attached' => [
        'library' => [
          'lib_core/unblibtabs',
          'lib_core/lib-chosen',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function getReservesForm() {
    $form_reserves =
      '<form action="//lib.unb.ca/core/action/process_reserves_search.php" id="searchReserves" method="get">
        <div class="form-group">
          <div class="form-row mb-1">
            <label class="ml-1" for="keywords"><b>Search by instructor, course name or course number:</b></label>
          </div>
          <div class="form-row">
            <div class="col-md-6 mb-2">
              <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fas fa-search"></i></div>
                </div>
                <input class="form-control" id="keywords" name="keywords" placeholder="Enter keywords" type="search" value="">
              </div>
            </div>
            <div class="col-md-4 mb-2">
              <label class="sr-only" for="semester">Course Semester</label>
              <select class="form-control" id="semester" name="semester">
                <option value="">All semesters</option>
                <option value="ON">Ongoing </option>
                <option value="2019SM" selected="selected">Summer 2019</option>
                <option value="2019WI">Winter 2019</option>
                <option value="2018FA">Fall 2018</option>
                <option value="2018FY">Full Year 2018</option>
                <option value="2018SM">Summer 2018</option>
                <option value="2018WI">Winter 2018</option>
                <option value="2017FY">Full Year 2017</option>
                <option value="2017FA">Fall 2017</option>
                <option value="2017SM">Summer 2017</option>
                <option value="2017WI">Winter 2017</option>
              </select>
            </div>
            <div class="col-md-2 mb-2">
              <button class="btn btn-primary" id="searchReservesSubmit" type="submit">GO</button>
            </div>
          </div>
        </div>
      </form>
      <div class="px-2">
        <a href="/reserves/index.php?h=1">
          <i class="fas fa-user"></i>
          Login to My UNB Reserves
        </a>
      </div>';

    return $form_reserves;
  }

  /**
   * {@inheritdoc}
   */
  protected function getCatalogueForm() {
    $form_catalogue =
      '<form accept-charset="UTF-8" action="/lib.unb.ca/core/inc-2015/UNB-WorldCat-Discovery-search.php" id="home_WCD" method="post" name="wcfw">
        <div class="form-group">
        <div class="form-row mb-2">
          <div class="col-lg-4">
            <b>Search UNB WorldCat:</b>
          </div>
          <div class="col-lg-8">
            <div class="form-check form-check-inline">
              <input checked="checked" class="form-check-input" id="scope_UNBLibraries_WCD" name="scope" type="radio" value="wz:66413">
              <label class="form-check-label" for="scope_UNBLibraries_WCD">UNB Libraries</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" id="scope_worldwide_WCD" name="scope" type="radio" value="">
              <label class="form-check-label" for="scope_worldwide_WCD">Libraries Worldwide</label>
            </div>
          </div>
        </div>
        <div class="form-row">
          <div class="col-md-6 mb-2">
            <label class="sr-only" for="queryString_WCD">
              Search for:
            </label>
            <div class="input-group mb-2">
              <div class="input-group-prepend">
                  <div class="input-group-text"><i class="fas fa-search"></i></div>
              </div>
              <input class="form-control" id="queryString_WCD" name="queryString" placeholder="Enter search terms" type="search">
            </div>
            <label class="sr-only" for="searchIndex_WCD">
              Search index
            </label>
          </div>
           <div class="col-md-4 mb-2">
            <select class="form-control" id="searchIndex_WCD" name="searchIndex">
              <option value="kw">keyword</option>
              <option value="ti">title</option>
              <option value="au">author</option>
              <option value="nu">call number</option>
              <option value="tj">journal title</option>
              <option value="su">subject</option>
            </select>
          </div>
           <div class="col-md-2 mb-1">
            <button class="btn btn-primary" id="search_WCD" title="Search" type="submit">GO</button>
          </div>
        </div>
        </div>
      </form>
      <div class="px-2">
        <a href="//unb.on.worldcat.org/advancedsearch">Advanced Search</a> |
        <a href="/about/loc_call/">Locations Guide</a> |
        <a href="/worldcat/FAQs.php" title="Using WorldCat Discovery">
          <i class="fas fa-question-circle"></i> Help
        </a>
      </div>';

    return $form_catalogue;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDatabasesForm() {
    $categories = _lib_core_get_guide_categories();
    $database_titles = _lib_core_get_database_titles();

    $subject_form =
      '<form class="categorySelect">
        <div class="form-group">
          <div class="form-row">
            <label class="ml-1 font-weight-bold" for="category">
              Browse databases by subject
            </label>
          </div>
          <div class="form-row">
            <div class="col-md-8 mb-1">
              <select class="chosen-select form-control" name="category">
                <option value="">Select a subject&hellip;</option>';
                foreach ($categories as $value => $label) {
                  $subject_form .=
                    '<option value="' .
                    $value . '">' .
                    $label .
                    '</option>';
                }
              $subject_form .=
              '</select>
            </div>
            <div class="col-md-2 mb-1">
                <button class="btn btn-primary" type="submit">GO</button>
            </div>
          </div>
        </div>
      </form>';

    $title_form =
      '<div class="divider"></div>
      <form id="title_results" method="get" action="/eresources/index.php">
        <div class="form-group">
          <div class="form-row">
            <label for="databaseID" class="ml-1 font-weight-bold">
              <span class="text-red">OR</span>&nbsp;&nbsp;Browse for databases by title
            </label>
          </div>
          <div class="form-row">
            <div class="col-md-10 mb-1">
              <select class="chosen-select form-control" id="databaseID" name="id">
                <option value="">Choose a database title</option>';
                foreach ($database_titles as $value => $label) {
                  $title_form .=
                    '<option value="' .
                    $value . '">' .
                    $label .
                    '</option>';
                }
              $title_form .=
              '</select>
            </div>
            <div class="col-md-2 mb-1">
              <input type="hidden" name="sub" value="indexes">
              <button class="btn btn-primary" type="submit">GO</button>
            </div>
          </div>
        </div>
      </form>
      <div class="px-2">
        <a href="/eresources/index.php?sub=indexes">
          <i class="fas fa-list-ul"></i>
          More Search Options
        </a>
      </div>';

    $form_databases = $subject_form . $title_form;
    return $form_databases;
  }

  /**
   * {@inheritdoc}
   */
  protected function getJournalsForm() {
    $form_journals =
      '<form action="/eresources/index.php" method="get" id="search_results_journals">
        <input type="hidden" name="sub" id="sub" value="journals">
        <div class="font-weight-bold mb-1">
          <label for="searchtype_every_journal">
            <input type="radio" checked="checked" value="every_word" name="searchtype" id="searchtype_every_journal" class="radioSelect mr-1">Word(s) in title
          </label>
          <label for="searchtype_browse_journal" class="ml-2">
            <input type="radio" value="browse" name="searchtype" id="searchtype_browse_journal" class="radioSelect mr-1">Starts with
          </label>
          <label for="searchtype_exact_journal" class="ml-2">
            <input type="radio" value="exact" name="searchtype" id="searchtype_exact_journal" class="radioSelect mr-1">Exact
          </label>
        </div>
        <p>
          <label for="title_journal" class="sr-only">title</label>
          <input id="title_journal" name="title" type="search" placeholder="Search for journal and newspaper titles">
          <input type="submit" value="GO" class="btn btn-primary">
        </p>
        <p class="pl-1">
          <a href="/eresources/index.php?sub=journals&amp;packages=Y">Journal Packages</a> |
          <a href="/eresources/index.php?sub=journals&amp;browseNewsColl=Y&amp;limitResourceType=enewsColl">Newspaper Packages</a> |
          <a href="/eresources/newspapers.php" title="Guide to finding newspapers at UNB Libraries">
          <i class="fas fa-question-circle"></i> Newspaper Guide</a>
        </p>
        <p class="moreOptions">
          <a href="/eresources/index.php?sub=journals">
            <i class="fas fa-list-ul"></i>
            More Search Options
          </a>
        </p>
      </form>';

    return $form_journals;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEncyclopediasForm() {
    $form_encyclopedias =
      '<form action="/eresources/index.php" method="get" id="search_results_refmat">
        <input type="hidden" name="sub" id="sub_refmat" value="refmat">
        <p>
          <small>Search for Reference Materials by title:</small>
        </h4>
        <div class="font-weight-bold mb-1">
          <label for="searchtype_every_refmat" class="mr-2">
            <input type="radio" checked="checked" value="every_word" name="searchtype" id="searchtype_every_refmat" class="radioSelect">
            Word(s) in title
          </label>
          <label for="searchtype_browse_refmat" class="mr-2">
            <input type="radio" value="browse" name="searchtype" id="searchtype_browse_refmat" class="radioSelect">
            Starts with
          </label>
          <label for="searchtype_exact_refmat">
            <input type="radio" value="exact" name="searchtype" id="searchtype_exact_refmat" class="radioSelect">
            Exact
          </label>
        </div>
        <div class="mb-1">
          <label for="title_refmat" class="sr-only">
            title
          </label>
          <input type="search" value="" name="title" id="title_refmat" placeholder="Search for encyclopedias, dictionaries, etc.">
          <input type="submit" value="GO" class="btn btn-primary">
        </div>
        <p class="pl-1 pb-2">
          <a href="/eresources/refguide.php" title="Guide to finding Reference Materials at UNB Libraries">
          <i class="fa fa-compass"></i> Reference Materials Guide</a> |
          <a href="https://guides.lib.unb.ca/guide/98">Browse dictionaries</a>
        </p>
        <p class="moreOptions">
          <a href="/eresources/index.php?sub=refmat">
            <i class="fas fa-th-list" aria-hidden="true"></i>
            More Search Options
          </a>
        </p>
      </form>';

    return $form_encyclopedias;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEbooksForm() {
    $form_ebooks =
      '<p>
        <small class="discoverNote">Search our vast electronic book collections for titles suitable for your computer, tablet or eReader.</small>
      </p>
      <form action="/eresources/index.php" method="get" id="search_results_ebooks">
        <input type="hidden" name="sub" id="sub_ebooks" value="ebooks">
        <div class="padHeight font-weight-bold">
          <label for="searchtype_every_ebooks" class="mr-2">
            <input type="radio" checked="checked" value="every_word" name="searchtype" id="searchtype_every_ebooks" class="radioSelect">
            Word(s) in title
          </label>
          <label for="searchtype_exact_ebooks" class="mr-2">
            <input type="radio" value="exact" name="searchtype" id="searchtype_exact_ebooks" class="radioSelect">
            Exact title
          </label>
          <label for="searchtype_keyword_ebooks">
            <input type="radio" value="keyword" name="searchtype" id="searchtype_keyword_ebooks" class="radioSelect">
            Keyword search (title, author, publisher&hellip;)
          </label>
        </div>
        <label for="title_ebooks" class="sr-only">
          title
        </label>
        <input type="search" value="" name="title" id="title_ebooks">
        <input type="submit" value="GO" class="btn btn-primary">
        <p class="mt-3">
          <a href="/eresources/index.php?sub=ebooks">
            <i class="fas fa-th-list"></i>
            Browse e-Book Collections
          </a>
        </p>
      </form>';

    return $form_ebooks;
  }

  /**
   * {@inheritdoc}
   */
  protected function getVideosForm() {
    $form_videos =
      '<p>
        <small class="discoverNote">Search across our online video collections.</small>
      </p>
      <form action="/eresources/index.php" id="search_results_video" method="get" >
        <div class="form-group">
          <input id="sub_video" name="sub" type="hidden" value="video">
          <div class="form-row mb-2">
            <div class="form-inline font-weight-bold">
              <div class="form-check ml-1">
                <input checked="checked" class="align-self-start form-check-input" id="searchtype_every_video" name="searchtype" type="radio" value="every_word">
                <label class="form-check-label mr-3" for="searchtype_every_video">
                  Word(s) in title
                </label>
                <input class="align-self-start form-check-input" id="searchtype_browse_video" name="searchtype" type="radio" value="browse">
                <label class="form-check-label mr-3" for="searchtype_browse_video">
                  Title starts with
                </label>
                <input class="align-self-start form-check-input" id="searchtype_exact_video" name="searchtype" type="radio" value="exact">
                <label class="form-check-label mr-3" for="searchtype_exact_video">
                  Exact title
                </label>
                <input class="align-self-start form-check-input" id="searchtype_keyword_video" name="searchtype" type="radio" value="keyword">
                <label class="form-check-label" for="searchtype_keyword_video">
                  Keyword search
                </label>
              </div>
            </div>
          </div>
          <div class="form-row">
            <label for="title_video" class="sr-only">
              Title
            </label>
            <div class="col-md-8 mb-2">
              <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fas fa-search"></i></div>
                </div>
                <input class="form-control" name="title"  id="title_video" type="search" value="">
              </div>
            </div>
            <div class="col-md-2 mb-2">
              <button class="btn btn-primary" type="submit">GO</button>
            </div>
          </div>
        </div>
        <div class="px-2">
          <a href="/eresources/index.php?sub=video">
            <i class="fas fa-th-list"></i>
            Browse Video Collections
          </a>
        </div>
      </form>';

    return $form_videos;
  }

}
