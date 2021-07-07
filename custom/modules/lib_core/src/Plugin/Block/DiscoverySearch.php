<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Component\Serialization\Json;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Provides the UNB Libraries Discovery Search Home Block.
 *
 * @Block(
 *   id = "discovery_search_block",
 *   admin_label = @Translation("UNB Libraries Discovery Search (Header)"),
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
      $askus_block = render($render_array);
    }

    $html =
      '<div class="Accordion d-flex flex-column flex-lg-row px-4 px-lg-0 theme-dark">
        <div id="discovery-search" class="flex-grow-1">
        <div class="card">
          <h2 class="sr-only">Search</h2>
          <div class="card-header px-2 pb-1">
            <nav class="navbar navbar-expand-md text-nowrap">
            <button class="navbar-toggler text-white" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                Select a search category
            </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                   <ul class="navbar-nav d-flex align-items-lg-end justify-content-between w-100">
                      <li class="nav-item"><button aria-controls="searchPanel1" aria-expanded="true" class="Accordion-trigger p-2" id="searchBtn1">Catalogue</button></li>
                      <li class="nav-item"><button aria-controls="searchPanel2" aria-expanded="false" class="Accordion-trigger p-2" id="searchBtn2" tabindex="-1">Reserves</button></li>
                      <li class="nav-item"><button aria-controls="searchPanel3" aria-expanded="false" class="Accordion-trigger p-2" id="searchBtn3" tabindex="-1">Article Databases</button></li>
                      <li class="nav-item"><button aria-controls="searchPanel4" aria-expanded="false" class="Accordion-trigger p-2 text-left text-lg-center" id="searchBtn4" tabindex="-1">Journals &amp; Newspapers</button></li>
                      <li class="nav-item">
                         <a class="btn Accordion-trigger p-2 rounded-0 shadow-none text-left text-lg-center text-nowrap"
                            id="searchBtn5" aria-controls="searchPanel5" aria-expanded="false" tabindex="-1"
                            href="https://web.lib.unb.ca/eresources"
                            onclick="location.href=\'https://web.lib.unb.ca/eresources\';"
                            title="Guide to Finding Reference Materials (opens new page)">More<i class="fas fa-angle-double-right fa-sm fa-muted ml-1" aria-hidden="true"></i>
                         </a>
                      </li>
                    </ul>
                </div>
             </nav>
          </div>
          <div class="card-body px-2 py-0">
            <div aria-labelledby="searchBtn1 dropdownBtn1" class="Accordion-panel" id="searchPanel1" role="region">' . $this->getCatalogueForm() . '</div>
            <div aria-labelledby="searchBtn2 dropdownBtn2" class="Accordion-panel" id="searchPanel2" role="region" hidden="">' . $this->getReservesForm() . '</div>
            <div aria-labelledby="searchBtn3 dropdownBtn3" class="Accordion-panel" id="searchPanel3" role="region" hidden="">' . $this->getDatabasesForm() . '</div>
            <div aria-labelledby="searchBtn4 dropdownBtn4" class="Accordion-panel" id="searchPanel4" role="region" hidden="">' . $this->getJournalsForm() . '</div>
            <div aria-labelledby="searchBtn5 dropdownBtn5" class="Accordion-panel" id="searchPanel5" role="region" hidden="">
              <div class="d-flex justify-content-center pt-4"><i class="fas fa-spinner fa-lg fa-spin" aria-hidden="true"></i></div>
            </div>
          </div>
        </div>
        </div>
        <div id="ask-us" class="d-none d-lg-block bg-askus p-3">' . $askus_block . '</div>
      </div>';

    return [
      '#children' => $html,
      '#attached' => [
        'library' => [
          'lib_core/lib-chosen',
          'lib_core/chosen-bootstrap',
          'lib_core/accessible-accordion',
          'lib_core/discoverysearch',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function getReservesForm() {
    $form_reserves =
      '<form action="//web.lib.unb.ca/reserves/index.php/quickSearch" id="searchReserves" class="chosen-compact mb-2" method="post">
        <div class="d-flex flex-column flex-md-row">
          <div class="flex-fill mb-2 mr-0 mr-md-2">
            <label class="sr-only" for="keywords">
              Search for:
            </label>
            <input class="form-control" id="keywords" name="keywords" placeholder="Search by instructor, course name, or course number" type="search" value="" required>
          </div>
          <div class="flex-fill mb-2 mr-0 mr-md-2">
            <label class="sr-only" for="semester">
              Course Semester
            </label>' .
            $this->getReservesSemesters() .
         '</div>
          <div class="mb-2">
            <button class="btn btn-primary form-control px-3" id="searchReservesSubmit" type="submit">GO</button>
          </div>
        </div>
      </form>
      <ul class="list inline m-0 p-0">
        <li class="list-inline-item mr-4 my-2 my-sm-1">
          <a href="//web.lib.unb.ca/reserves/index.php?h=1"><i class="fas fa-sign-in-alt" aria-hidden="true"></i>
            Login to My UNB Reserves
          </a>
        </li>
      </ul>';

    return $form_reserves;
  }

  /**
   * {@inheritdoc}
   */
  protected function getReservesSemesters() {
    // Set default empty option.
    $options = [
      '' => 'All semesters',
    ];
    $default_value = '';

    try {
      $response = \Drupal::httpClient()
        ->get('https://web.lib.unb.ca/reserves/index.php/semester', [
          'headers' => [
            'Accept' => 'application/vnd.api+json',
          ],
        ]);
      $json_string = (string) $response->getBody();
      $json = Json::decode($json_string);

      if (empty($json_string) || empty($json)) {
        // Log empty response.
        $msg = "Empty Reserves/Semester JSON response!";
        \Drupal::logger('lib_core')->notice($msg);
      }
      else {
        foreach ($json as $key => $value) {
          $term_year = empty($value['year']) ? '' : ' ' . $value['year'];
          $options[$key] = $value['termName'] . $term_year;
          if ($value['isCurrent']) {
            $default_value = $key;
          }
        }
      }
    }
    catch (GuzzleException $e) {
      // Log response error.
      $msg = "Reserves/Semester JSON response error: " . $e;
      \Drupal::logger('lib_core')->error($msg);
    }

    $semesters = [
      '#type' => 'select',
      '#options' => $options,
      // #default_value doesn't work.
      '#value' => $default_value,
      '#attributes' => [
        'class' => [
          'custom-chosen-select',
          'form-control',
        ],
        'id' => 'semester',
        'name' => 'semester',
      ],
    ];

    return \Drupal::service('renderer')->render($semesters);
  }

  /**
   * {@inheritdoc}
   */
  protected function getCatalogueForm() {
    $form_catalogue =
      '<form accept-charset="UTF-8" action="/worldcat-search-helper" id="home_WCD" class="chosen-compact mb-2" method="post" name="wcfw">
          <input type="hidden" id="scope_UNBLibraries_WCD" name="scope" value="wz:66413">
          <div class="d-flex flex-column flex-md-row">
            <div class="flex-fill mb-2 mr-0 mr-md-2">
              <label class="sr-only" for="queryString_WCD">
                Search UNB WorldCat for:
              </label>
              <input class="form-control" id="queryString_WCD" name="queryString" placeholder="Search books, articles, and more" type="search" required>
            </div>
            <div class="flex-fill mb-2 mr-0 mr-md-2">
              <label class="sr-only" for="searchIndex_WCD">
                Search index
              </label>
              <fieldset class="form-type-select form-group">
                <select class="custom-chosen-select form-control" id="searchIndex_WCD" name="searchIndex">
                  <option value="kw">keyword</option>
                  <option value="ti">title</option>
                  <option value="au">author</option>
                  <option value="nu">call number</option>
                  <option value="tj">journal title</option>
                  <option value="su">subject</option>
                </select>
              </fieldset>
            </div>
            <div class="mb-2">
              <button class="btn btn-primary form-control px-3" id="search_WCD" title="Search" type="submit">GO</button>
            </div>
          </div>
      </form>
      <ul class="list inline m-0 p-0">
        <li class="list-inline-item mr-4 my-2 my-sm-1">
            <a href="//unb.on.worldcat.org/advancedsearch">Advanced Search</a>
        </li>    
        <li class="list-inline-item mr-4 my-2 my-sm-1">
            <a href="/worldcat/unb-worldcat-frequently-asked-questions">What Am I Searching?</a>
        </li>
        <li class="list-inline-item mr-4 my-2 my-sm-1">
            <a href="/about/loc_call">Locations Guide</a>
        </li>
      </ul>';

    return $form_catalogue;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDatabasesForm() {
    $title_form =
      '<form action="https://web.lib.unb.ca/eresources/index.php?sub=video/eresources/index.php" id="title_results" class="chosen-compact mb-2" method="get">
        <div class="d-flex flex-column flex-lg-row">
          <div class="flex-fill mb-2 mr-0 mr-lg-2">
            <label class="sr-only" for="databaseID">
              Browse for databases by title
            </label>' .
            $this->getDatabasesTitles() .
          '</div>
          <div class="mb-2">
            <input type="hidden" name="sub" value="indexes">
            <button class="btn btn-primary form-control px-3" type="submit">GO</button>
          </div>
        </div>
      </form>
      <ul class="list inline m-0 p-0">
        <li class="list-inline-item mr-4 my-2 my-sm-1">
          <a href="https://web.lib.unb.ca/eresources/index.php?sub=indexes">
            More Search Options
          </a>
        </li>
      </ul>';

    $form_databases = $title_form;
    return $form_databases;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDatabasesSubjects() {
    // Set default empty option.
    $options = [
      '' => '- Please choose an option -',
    ];

    $categories = _lib_core_get_guide_categories();
    foreach ($categories as $value => $label) {
      $options[$value] = $label;
    }

    $subjects = [
      '#type' => 'select',
      '#required' => 'TRUE',
      '#options' => $options,
      '#attributes' => [
        'class' => [
          'custom-chosen-select',
          'form-control',
        ],
        'id' => 'database-subjects',
        'name' => 'category',
      ],
    ];

    return \Drupal::service('renderer')->render($subjects);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDatabasesTitles() {
    // Set default empty option.
    $options = [
      '' => 'Please choose a database title...',
    ];

    try {
      $response = \Drupal::httpClient()
        ->get('https://web.lib.unb.ca/eresources/databases.php', [
          'headers' => [
            'Accept' => 'application/vnd.api+json',
          ],
        ]);
      $json_string = (string) $response->getBody();
      $json = Json::decode($json_string);

      if (empty($json_string) || empty($json)) {
        // Log empty response.
        $msg = "Empty Databases/Titles JSON response!";
        \Drupal::logger('lib_core')->notice($msg);
      }
      else {
        foreach ($json['databases'] as $value) {
          $options[$value["value"]] = $value["name"];
        }
      }
    }
    catch (GuzzleException $e) {
      // Log response error.
      $msg = "Databases/Titles JSON response error: " . $e;
      \Drupal::logger('lib_core')->error($msg);
    }

    $titles = [
      '#type' => 'select',
      '#required' => 'TRUE',
      '#options' => $options,
      '#attributes' => [
        'id' => 'databaseID',
        'name' => 'id',
        'class' => [
          'custom-chosen-select',
          'form-control',
          'required',
        ],
        'aria-required' => 'true',
        'required' => 'required',
      ],
    ];

    return \Drupal::service('renderer')->render($titles);
  }

  /**
   * {@inheritdoc}
   */
  protected function getJournalsForm() {
    $form_journals =
      '<form action="https://web.lib.unb.ca/eresources/index.php" id="search_results_journals" class="mb-2" method="get">
        <input id="sub" name="sub" type="hidden" value="journals">
        <input id="searchtype_every_journal" name="searchtype" type="hidden" value="every_word">

        <div class="d-flex flex-column flex-lg-row">
          <div class="flex-fill mb-2 mr-0 mr-lg-2">        
            <fieldset class="form-group">
            <label class="sr-only" for="title_journal">
              Search words in title
            </label>
            <input class="form-control" id="title_journal" name="title" placeholder="Search for journal and newspaper titles" type="search" required>
            </fieldset>
          </div>
          <div class="mb-2">
            <button class="btn btn-primary form-control px-3" type="submit">GO</button>
          </div>
        </div>
      </form>
      <ul class="list inline m-0 p-0">
        <li class="list-inline-item mr-4 my-2 my-sm-1">
            <a href="https://web.lib.unb.ca/eresources/index.php?sub=journals">
                More Search Options
            </a>
        </li>
        <li class="list-inline-item mr-4 my-2 my-sm-1">
            <a href="https://web.lib.unb.ca/eresources/newspapers.php" title="Guide to finding newspapers at UNB Libraries">
                Newspaper Guide
            </a>
        </li>
      </ul>';

    return $form_journals;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEncyclopediasForm() {
    $form_encyclopedias =
      '<form action="https://web.lib.unb.ca/eresources/index.php" method="get" id="search_results_refmat">
        <input id="sub_refmat" name="sub" type="hidden" value="refmat">
        <div class="form-group">
          <fieldset>
          <legend class="custom-legend mb-4">Search for Reference Materials by title<span class="sr-only"> using 1 of the following options</span>.</legend>
            <div class="form-row font-weight-bold ml-1">
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0">
                <input checked="checked" class="custom-control-input" id="searchtype_every_refmat" name="searchtype" type="radio" value="every_word">
                <label class="custom-control-label" for="searchtype_every_refmat">
                  Word(s) in title
                </label>
              </div>
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0">
                <input class="custom-control-input" id="searchtype_browse_refmat" name="searchtype" type="radio" value="browse">
                <label class="custom-control-label" for="searchtype_browse_refmat">
                  Starts with
                </label>
              </div>
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0">
                <input class="custom-control-input" id="searchtype_exact_refmat" name="searchtype" type="radio" value="exact">
                <label class="custom-control-label" for="searchtype_exact_refmat">
                  Exact
                </label>
              </div>
            </div>
          </fieldset>
        </div>
        <div class="form-group">
          <div class="form-row">
            <label class="sr-only" for="title_refmat">
              Title
            </label>
            <div class="col-md-10 mb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">
                    <i class="fas fa-search"></i>
                  </div>
                </div>
                <input class="form-control" id="title_refmat" name="title" placeholder="Search for encyclopedias, dictionaries, etc." type="search" value="" required>
              </div>
            </div>
            <div class="col-md-2 mb-2">
              <button class="btn btn-primary form-control px-3" type="submit">GO</button>
            </div>
          </div>
        </div>
        </form>
        <div class="p-2">
          <a href="https://web.lib.unb.ca/eresources/refguide.php">
            <i class="fas fa-compass"></i>
            Reference Materials Guide
          </a>
          |
          <a href="//guides.lib.unb.ca/guide/98">
            Browse dictionaries
          </a>
        </div>
        <div class="px-2">
          <a href="https://web.lib.unb.ca/eresources/index.php?sub=refmat">
            <i class="fas fa-search-plus" aria-hidden="true"></i>
            More Search Options
          </a>
        </div>';

    return $form_encyclopedias;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEbooksForm() {
    $form_ebooks =
      '<form action="https://web.lib.unb.ca/eresources/index.php" id="search_results_ebooks" method="get">
        <input id="sub_ebooks" name="sub" type="hidden" value="ebooks">
        <div class="form-group">
          <fieldset>
            <legend class="custom-legend mb-4">Search our vast electronic book collections for titles suitable for your computer,
                tablet or eReader<span class="sr-only"> using 1 of the following options</span>.</legend>
            <div class="form-row font-weight-bold ml-1">
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0">
                <input checked="checked" class="custom-control-input" id="searchtype_every_ebooks" name="searchtype" type="radio" value="every_word">
                <label class="custom-control-label" for="searchtype_every_ebooks">
                  Word(s) in title
                </label>
              </div>
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0">
                <input class="custom-control-input" id="searchtype_exact_ebooks" name="searchtype" type="radio" value="exact">
                <label class="custom-control-label" for="searchtype_exact_ebooks">
                  Exact title
                </label>
              </div>
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0">
                <input class="custom-control-input" id="searchtype_keyword_ebooks" name="searchtype" type="radio" value="keyword">
                <label class="custom-control-label" for="searchtype_keyword_ebooks">
                  Keyword search (title, author, publisher&hellip;)
                </label>
              </div>
            </div>
          </fieldset>
        </div>
        <div class="form-group">
          <div class="form-row">
            <label class="sr-only" for="title_ebooks">
              Title
            </label>
            <div class="col-md-10 mb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">
                    <i class="fas fa-search"></i>
                  </div>
                </div>
                <input class="form-control" id="title_ebooks" name="title" placeholder="Enter search terms" type="search" value="" required>
              </div>
            </div>
            <div class="col-md-2 mb-2">
              <button class="btn btn-primary form-control px-3" type="submit">GO</button>
            </div>
          </div>
        </div>
      </form>
      <div class="p-2">
        <a href="https://web.lib.unb.ca/eresources/index.php?sub=ebooks">
          <i class="fas fa-th-list"></i>
          Browse e-Book Collections
        </a>
      </div>';

    return $form_ebooks;
  }

  /**
   * {@inheritdoc}
   */
  protected function getVideosForm() {
    $form_videos =
      '<form action="https://web.lib.unb.ca/eresources/index.php" id="search_results_video" method="get">
        <input id="sub_video" name="sub" type="hidden" value="video">
        <div class="form-group">
          <fieldset>
          <legend class="custom-legend mb-4">Search across our online video
            collections<span class="sr-only">using 1 of the following options</span>.</legend>
            <div class="form-row font-weight-bold ml-1">
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0">
                <input checked="checked" class="custom-control-input" id="searchtype_every_video" name="searchtype" type="radio" value="every_word">
                <label class="custom-control-label" for="searchtype_every_video">
                  Word(s) in title
                </label>
              </div>
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0">
                <input class="custom-control-input" id="searchtype_browse_video" name="searchtype" type="radio" value="browse">
                <label class="custom-control-label" for="searchtype_browse_video">
                  Title starts with
                </label>
              </div>
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0">
                <input class="custom-control-input" id="searchtype_exact_video" name="searchtype" type="radio" value="exact">
                <label class="custom-control-label" for="searchtype_exact_video">
                  Exact title
                </label>
              </div>
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0">
                <input class="custom-control-input" id="searchtype_keyword_video" name="searchtype" type="radio" value="keyword">
                <label class="custom-control-label" for="searchtype_keyword_video">
                  Keyword search
                </label>
              </div>
            </div>
          </fieldset>
         </div>
         <div class="form-group">
          <div class="form-row">
            <label for="title_video" class="sr-only">
              Title
            </label>
            <div class="col-md-9 mb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                      <i class="fas fa-search"></i>
                    </div>
                </div>
                <input class="form-control" name="title" id="title_video" placeholder="Enter search terms" type="search" value="" required>
              </div>
            </div>
            <div class="col-md-2 mb-2">
              <button class="btn btn-primary form-control px-3" type="submit">GO</button>
            </div>
          </div>
        </div>
        <div class="p-2">
          <a href="https://web.lib.unb.ca/eresources/index.php?sub=video">
            <i class="fas fa-th-list"></i>
            Browse Video Collections
          </a>
        </div>
      </form>';

    return $form_videos;
  }

}
