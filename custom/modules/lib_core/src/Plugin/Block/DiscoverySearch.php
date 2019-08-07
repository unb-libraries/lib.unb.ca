<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Component\Serialization\Json;

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
      '<div class="theme-dark">
        <div id="discovery-search" class="tabs-accordian">
          <h2 class="sr-only">Search</h2>
          <ul id="tabs" class="flex-fill nav nav-tabs justify-content-center" role="tablist">
            <li class="nav-item">
              <a id="tab-reserves" href="#pane-reserves" class="nav-link" data-toggle="tab" role="tab">
                Reserves
              </a>
            </li>
            <li class="nav-item">
              <a id="tab-catalogue" href="#pane-catalogue" class="nav-link active" data-toggle="tab" role="tab">
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
            <div class="flex-grow-1 tab-content">
              <div aria-labelledby="tab-reserves" class="card tab-pane fade show" id="pane-reserves" role="tabpanel">
                <div class="card-header p-0" id="heading-reserves">
                  <h3 class="m-0">
                    <a aria-controls="collapse-reserves" aria-expanded="false" class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-reserves" role="button">
                      Reserves
                    </a>
                  </h3>
                </div>
                <div class="collapse" data-parent="#content" id="collapse-reserves">
                  <div class="card-body">';
                    $html .= $this->getReservesForm();
                    $html .=
                  '</div>
                </div>
              </div>
  
              <div aria-labelledby="tab-catalogue" class="card tab-pane fade active show" id="pane-catalogue" role="tabpanel">
                <div class="card-header p-0" id="heading-catalogue">
                  <h3 class="m-0">
                    <a aria-controls="collapse-catalogue" aria-expanded="true" class="nav-link mx-1 p-2" data-toggle="collapse" href="#collapse-catalogue" role="button">
                      Catalogue
                    </a>
                  </h3>
                </div>
                <div class="collapse show" data-parent="#content" id="collapse-catalogue">
                  <div class="card-body">';
                    $html .= $this->getCatalogueForm();
                    $html .=
                  '</div>
                </div>
              </div>
  
              <div aria-labelledby="tab-databases" class="card tab-pane fade" id="pane-databases" role="tabpanel">
                <div class="card-header p-0" id="heading-databases">
                  <h3 class="m-0">
                    <a aria-controls="collapse-databases" aria-expanded="false" class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-databases" role="button">
                      <span class="d-none d-sm-inline">Article</span> Databases
                    </a>
                  </h3>
                </div>
                <div class="collapse" data-parent="#content" id="collapse-databases">
                  <div class="card-body">';
                    $html .= $this->getDatabasesForm();
                    $html .=
                  '</div>
                </div>
              </div>
              
              <div aria-labelledby="tab-journals" class="card tab-pane fade" id="pane-journals" role="tabpanel">
                <div class="card-header p-0" id="heading-journals">
                  <h3 class="m-0">
                    <a aria-controls="collapse-journals" aria-expanded="false" class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-journals" role="button">
                      Journals<span class="d-none d-sm-inline"> &amp; Newspapers</span>
                    </a>
                  </h3>
                </div>
                  <div class="collapse" data-parent="#content" id="collapse-journals">
                    <div class="card-body">';
                      $html .= $this->getJournalsForm();
                      $html .=
                    '</div>
                  </div>
              </div>
  
              <div aria-labelledby="tab-encyclopedias" class="card tab-pane fade" id="pane-encyclopedias" role="tabpanel">
                <div class="card-header p-0" id="heading-encyclopedias">
                  <h3 class="m-0">
                    <a aria-controls="collapse-encyclopedias" aria-expanded="false" class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-encyclopedias" role="button">
                      e-Encyclopedias<span class="d-none d-sm-inline">, etc.</span>
                    </a>
                  </h3>
                </div>
                  <div class="collapse" data-parent="#content" id="collapse-encyclopedias">
                    <div class="card-body">';
                      $html .= $this->getEncyclopediasForm();
                      $html .=
                    '</div>
                  </div>
              </div>
  
              <div aria-labelledby="tab-ebooks" class="card tab-pane fade" id="pane-ebooks" role="tabpanel">
                <div class="card-header p-0" id="heading-ebooks">
                  <h3 class="m-0">
                    <a aria-controls="collapse-ebooks" aria-expanded="false" class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-ebooks" role="button">
                      e-Books
                    </a>
                  </h3>
                </div>
                <div class="collapse" data-parent="#content" id="collapse-ebooks">
                  <div class="card-body">';
                    $html .= $this->getEbooksForm();
                    $html .=
                  '</div>
                </div>
              </div>
  
              <div aria-labelledby="tab-videos" class="card tab-pane fade" id="pane-videos" role="tabpanel">
                <div class="card-header p-0" id="heading-videos">
                  <h3 class="m-0">
                    <a aria-controls="collapse-videos" aria-expanded="false" class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-videos" role="button">
                      Videos
                    </a>
                  </h3>
                </div>
                <div class="collapse" data-parent="#content" id="collapse-videos">
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
      </div>
    </div>';

    return [
      '#children' => $html,
      '#attached' => [
        'library' => [
          'lib_core/unblibtabs',
          'lib_core/lib-chosen',
          'lib_core/chosen-bootstrap',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function getReservesForm() {
    $form_reserves =
      '<form action="//web.lib.unb.ca/core/action/process_reserves_search.php" id="searchReserves" method="get">
        <div class="form-group">
          <div class="form-row font-weight-bold mb-2 ml-1">
            <div class="mb-1">
                Search by instructor, course name or course number:
            </div>
          </div>
          <div class="form-row">
            <div class="col-md-6 mb-2">
              <label class="sr-only" for="queryString_WCD">
                Search for:
              </label>
              <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fas fa-search"></i></div>
                </div>
                <input class="form-control" id="keywords" name="keywords" placeholder="Enter keywords" type="search" value="">
              </div>
            </div>
            <div class="col-md-4 mb-2">
              <label class="sr-only" for="semester">
                Course Semester
              </label>';
              $form_reserves .= $this->getReservesSemesters();
            $form_reserves .=
            '</div>
            <div class="col-md-2 mb-2">
              <button class="btn btn-primary" id="searchReservesSubmit" type="submit">GO</button>
            </div>
          </div>
        </div>
      </form>
      <div class="px-2">
        <a href="//web.lib.unb.ca/reserves/index.php?h=1">
          <i class="fas fa-sign-in-alt"></i>
          Login to My UNB Reserves
        </a>
      </div>';

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
        ->get('//web.lib.unb.ca/reserves/index.php/semester', [
            'headers' => [
              'Accept' => 'application/vnd.api+json',
            ],
          ]
        );
      $json_string = (string) $response->getBody();
      if (empty($json_string)) {
        // Log empty response.
        $msg = "Empty Reserves/Semester JSON response!";
        \Drupal::logger('lib_core')->notice($msg);
      }
      else {
        $json = Json::decode($json_string);

        foreach ($json as $key => $value) {
          $term_year = empty($value['year']) ? '' : ' ' . $value['year'];
          $options[$key] = $value['termName'] . $term_year;
          if ($value['isCurrent']) {
              $default_value = $key;
          }
        }
      }
    }
    catch (RequestException $e) {
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
          'chosen-select',
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
      '<form accept-charset="UTF-8" action="https://web.lib.unb.ca/core/inc-2015/UNB-WorldCat-Discovery-search.php" id="home_WCD" method="post" name="wcfw">
        <div class="form-group">
          <div class="form-row font-weight-bold mb-2 ml-1">
            <div class="mb-1 mr-3">
              Search UNB WorldCat:
            </div>
            <div>
              <div class="custom-control custom-radio custom-control-inline">
                <input checked="checked" class="custom-control-input" id="scope_UNBLibraries_WCD" name="scope" type="radio" value="wz:66413">
                <label class="custom-control-label" for="scope_UNBLibraries_WCD">UNB Libraries</label>
              </div>
              <div class="custom-control custom-radio custom-control-inline">
                <input class="custom-control-input" id="scope_worldwide_WCD" name="scope" type="radio" value="">
                <label class="custom-control-label" for="scope_worldwide_WCD">Libraries Worldwide</label>
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="col-md-6 mb-2">
              <label class="sr-only" for="queryString_WCD">
                Search for:
              </label>
              <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fas fa-search"></i></div>
                </div>
                <input class="form-control" id="queryString_WCD" name="queryString" placeholder="Enter search terms" type="search" required>
              </div>
            </div>
            <div class="col-md-4 mb-2">
              <label class="sr-only" for="searchIndex_WCD">
                Search index
              </label>
              <select class="form-control" id="searchIndex_WCD" name="searchIndex">
                <option value="kw">keyword</option>
                <option value="ti">title</option>
                <option value="au">author</option>
                <option value="nu">call number</option>
                <option value="tj">journal title</option>
                <option value="su">subject</option>
              </select>
            </div>
             <div class="col-md-2 mb-2">
              <button class="btn btn-primary" id="search_WCD" title="Search" type="submit">GO</button>
            </div>
          </div>
        </div>
      </form>
      <div class="p-2">
        <a href="//unb.on.worldcat.org/advancedsearch">
          Advanced Search
        </a>
        |
        <a href="https://web.lib.unb.ca/about/loc_call">
          Locations Guide
        </a>
        |
        <a href="https://web.lib.unb.ca/worldcat/FAQs.php" title="Using WorldCat Discovery">
          <i class="fas fa-question-circle"></i>
          Help
        </a>
      </div>';

    return $form_catalogue;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDatabasesForm() {
    $subject_form =
      '<form id="category-select">
        <div class="form-group">
          <div class="form-row font-weight-bold">
            <label class="ml-2" for="category">
              Browse databases by subject
            </label>
          </div>
          <div class="form-row">
            <div class="col-md-10 input-group flex-nowrap mb-1">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  <i class="fas fa-list-ul"></i>
                </span>
              </div>';
              $subject_form .= $this->getDatabasesSubjects();
              $subject_form .=
            '</div>
            <div class="col-md-2 mb-1">
                <button class="btn btn-primary" type="submit">GO</button>
            </div>
          </div>
        </div>
      </form>';

    $title_form =
      '<div class="divider"></div>
      <form action="https://web.lib.unb.ca/eresources/index.php?sub=video/eresources/index.php" id="title_results" method="get">
        <div class="form-group">
          <div class="form-row font-weight-bold">
            <label class="ml-2" for="databaseID">
              <span class="text-red">OR</span>&nbsp;&nbsp;Browse for databases by title
            </label>
          </div>
          <div class="form-row">
            <div class="col-md-10 input-group flex-nowrap mb-1">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  <i class="fas fa-list-ul"></i>
                </span>
              </div>';
            $title_form .= $this->getDatabasesTitles();
            $title_form .=
            '</div>
            <div class="col-md-2 mb-1">
              <input type="hidden" name="sub" value="indexes">
              <button class="btn btn-primary" type="submit">GO</button>
            </div>
          </div>
        </div>
      </form>
      <div class="p-2">
        <a href="https://web.lib.unb.ca/eresources/index.php?sub=indexes">
          <i class="fas fa-search-plus"></i>
          More Search Options
        </a>
      </div>';

    $form_databases = $subject_form . $title_form;
    return $form_databases;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDatabasesSubjects() {
    // Set default empty option.
    $options = [
      '' => 'Select a subject',
    ];

    $categories = _lib_core_get_guide_categories();
    foreach ($categories as $value => $label) {
       $options[$value] = $label;
    }

    $subjects = [
      '#type' => 'select',
      '#options' => $options,
      '#attributes' => [
        'class' => [
          'chosen-select',
          'form-control',
        ],
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
      '' => 'Choose a database title',
    ];

    try {
      $response = \Drupal::httpClient()
        ->get('http://web.lib.unb.ca/eresources/databases.php', [
            'headers' => [
              'Accept' => 'application/vnd.api+json',
            ],
          ]
        );
      $json_string = (string) $response->getBody();
      if (empty($json_string)) {
        // Log empty response.
        $msg = "Empty Databases/Titles JSON response!";
        \Drupal::logger('lib_core')->notice($msg);
      }
      else {
        $json = Json::decode($json_string);

        foreach ($json['databases'] as $key => $value) {
          $options[$value["value"]] = $value["name"];
        }
      }
    }
    catch (RequestException $e) {
      // Log response error.
      $msg = "Databases/Titles JSON response error: " . $e;
      \Drupal::logger('lib_core')->error($msg);
    }

    $titles = [
      '#type' => 'select',
      '#options' => $options,
      '#attributes' => [
        'class' => [
          'chosen-select',
          'form-control',
        ],
        'id' => 'databaseID',
        'name' => 'id',
      ],
    ];

    return \Drupal::service('renderer')->render($titles);
  }

  /**
   * {@inheritdoc}
   */
  protected function getJournalsForm() {
    $form_journals =
      '<form action="https://web.lib.unb.ca/eresources/index.php?sub=video/eresources/index.php" id="search_results_journals" method="get">
        <input id="sub" name="sub" type="hidden" value="journals">
        <div class="form-group mt-1">
          <div class="form-row font-weight-bold ml-1">
            <div class="custom-control custom-radio custom-control-inline">
              <input checked="checked" class="custom-control-input" id="searchtype_every_journal" name="searchtype" type="radio" value="every_word">
              <label class="custom-control-label" for="searchtype_every_journal">
                Word(s) in title
              </label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input class="custom-control-input" name="searchtype" id="searchtype_browse_journal" type="radio" value="browse">
              <label class="custom-control-label" for="searchtype_browse_journal">
                Starts with
              </label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input class="custom-control-input" name="searchtype" id="searchtype_exact_journal" type="radio" value="exact">
              <label class="custom-control-label" for="searchtype_exact_journal">
                Exact
              </label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="form-row">        
            <label class="sr-only" for="title_journal">
              Title
            </label>
            <div class="col-md-9 mb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">
                    <i class="fas fa-search"></i>
                  </div>
                </div>
                <input class="form-control" id="title_journal" name="title" placeholder="Search for journal and newspaper titles" type="search">
              </div>
            </div>
            <div class="col-md-2 mb-2">
              <button class="btn btn-primary" type="submit" >GO</button>
            </div>
          </div>
        </div>
      </form>
      <div class="p-2">
          <a href="https://web.lib.unb.ca/eresources/index.php?sub=journals&packages=Y">
            Journal Packages
          </a>
          |
          <a href="https://web.lib.unb.ca/eresources/index.php?sub=journals&browseNewsColl=Y&limitResourceType=enewsColl">
            Newspaper Packages
          </a>
          |
          <a href="https://web.lib.unb.ca/eresources/newspapers.php" title="Guide to finding newspapers at UNB Libraries">
            <i class="fas fa-question-circle"></i>
            Newspaper Guide
          </a>
      </div>
      <div class="px-2">
        <a href="https://web.lib.unb.ca/eresources/index.php?sub=journals">
          <i class="fas fa-search-plus"></i>
          More Search Options
        </a>
      </div>';

    return $form_journals;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEncyclopediasForm() {
    $form_encyclopedias =
      '<p>
        <small>Search for Reference Materials by title:</small>
      </p>
      <form action="https://web.lib.unb.ca/eresources/index.php" method="get" id="search_results_refmat">
        <input id="sub_refmat" name="sub" type="hidden" value="refmat">
        <div class="form-group">
          <div class="form-row font-weight-bold ml-1">
            <div class="custom-control custom-radio custom-control-inline">
              <input checked="checked" class="custom-control-input" id="searchtype_every_refmat" name="searchtype" type="radio" value="every_word">
              <label class="custom-control-label" for="searchtype_every_refmat">
                Word(s) in title
              </label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input class="custom-control-input" id="searchtype_browse_refmat" name="searchtype" type="radio" value="browse">
              <label class="custom-control-label" for="searchtype_browse_refmat">
                Starts with
              </label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input class="custom-control-input" id="searchtype_exact_refmat" name="searchtype" type="radio" value="exact">
              <label class="custom-control-label" for="searchtype_exact_refmat">
                Exact
              </label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="form-row">
            <label class="sr-only" for="title_refmat">
              Title
            </label>
            <div class="col-md-9 mb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">
                    <i class="fas fa-search"></i>
                  </div>
                </div>
                <input class="form-control" id="title_refmat" name="title" placeholder="Search for encyclopedias, dictionaries, etc." type="search" value="">
              </div>
            </div>
            <div class="col-md-2 mb-2">
              <button class="btn btn-primary" type="submit">GO</button>
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
      '<p>
        <small>Search our vast electronic book collections for titles suitable for your computer, tablet or eReader.</small>
      </p>
      <form action="https://web.lib.unb.ca/eresources/index.php" id="search_results_ebooks" method="get">
        <input id="sub_ebooks" name="sub" type="hidden" value="ebooks">
        <div class="form-group">
          <div class="form-row font-weight-bold ml-1">
            <div class="custom-control custom-radio custom-control-inline">
              <input checked="checked" class="custom-control-input" id="searchtype_every_ebooks" name="searchtype" type="radio" value="every_word">
              <label class="custom-control-label" for="searchtype_every_ebooks">
                Word(s) in title
              </label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input class="custom-control-input" id="searchtype_exact_ebooks" name="searchtype" type="radio" value="exact">
              <label class="custom-control-label" for="searchtype_exact_ebooks">
                Exact title
              </label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input class="custom-control-input" id="searchtype_keyword_ebooks" name="searchtype" type="radio" value="keyword">
              <label class="custom-control-label" for="searchtype_keyword_ebooks">
                Keyword search (title, author, publisher&hellip;)
              </label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="form-row">
            <label class="sr-only" for="title_ebooks">
              Title
            </label>
            <div class="col-md-9 mb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">
                    <i class="fas fa-search"></i>
                  </div>
                </div>
                <input class="form-control" id="title_ebooks" name="title" placeholder="Enter search terms" type="search" value="">
              </div>
            </div>
            <div class="col-md-2 mb-2">
              <button class="btn btn-primary" type="submit">GO</button>
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
      '<p>
        <small>Search across our online video collections.</small>
      </p>
      <form action="https://web.lib.unb.ca/eresources/index.php" id="search_results_video" method="get">
        <input id="sub_video" name="sub" type="hidden" value="video">
        <div class="form-group">
          <div class="form-row font-weight-bold ml-1">
            <div class="custom-control custom-radio custom-control-inline">
              <input checked="checked" class="custom-control-input" id="searchtype_every_video" name="searchtype" type="radio" value="every_word">
              <label class="custom-control-label" for="searchtype_every_video">
                Word(s) in title
              </label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input class="custom-control-input" id="searchtype_browse_video" name="searchtype" type="radio" value="browse">
              <label class="custom-control-label" for="searchtype_browse_video">
                Title starts with
              </label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input class="custom-control-input" id="searchtype_exact_video" name="searchtype" type="radio" value="exact">
              <label class="custom-control-label" for="searchtype_exact_video">
                Exact title
              </label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input class="custom-control-input" id="searchtype_keyword_video" name="searchtype" type="radio" value="keyword">
              <label class="custom-control-label" for="searchtype_keyword_video">
                Keyword search
              </label>
            </div>
          </div>
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
                <input class="form-control" name="title" id="title_video" placeholder="Enter search terms" type="search" value="">
              </div>
            </div>
            <div class="col-md-2 mb-2">
              <button class="btn btn-primary" type="submit">GO</button>
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
