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

      $askus_block = '<div class="d-none d-lg-block col-lg-4 p-4">';
      $askus_block .= render($render_array);
      $askus_block .= '</div>';
    }

    $html =
      '<div class="Accordion mt-0 mt-lg-5 theme-dark d-flex flex-column flex-lg-row">
        <div id="discovery-search" class="flex-grow-1">
        <div class="card">
          <h2 class="sr-only">Search</h2>
          <div class="card-header px-0 pb-1">
             <nav class="navbar navbar-expand-lg">
                <!--<button aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler" data-target="#navbarNav" data-toggle="collapse" type="button"><span class="mr-1 navbar-toggler-icon"></span>Search Menu</button>
                <div class="collapse navbar-collapse" id="navbarNav">-->
                   <ul class="navbar-nav d-flex justify-content-around align-items-lg-end w-100">
                      <li class="nav-item"><button aria-controls="searchPanel1" aria-expanded="true" class="Accordion-trigger p-2" id="searchBtn1">Catalogue</button></li>
                      <li class="nav-item"><button aria-controls="searchPanel2" aria-expanded="false" class="Accordion-trigger p-2" id="searchBtn2" tabindex="-1">Reserves</button></li>
                      <li class="nav-item"><button aria-controls="searchPanel3" aria-expanded="false" class="Accordion-trigger p-2" id="searchBtn3" tabindex="-1">Article Databases</button></li>
                      <li class="nav-item"><button aria-controls="searchPanel4" aria-expanded="false" class="Accordion-trigger p-2" id="searchBtn4" tabindex="-1">Journals &amp; Newspapers</button></li>
                      <li class="nav-item"><button aria-controls="searchPanel5" aria-expanded="false" class="Accordion-trigger p-2" id="searchBtn5" tabindex="-1">e-Reference Materials</button></li>
                      <li class="nav-item"><button aria-controls="searchPanel6" aria-expanded="false" class="Accordion-trigger p-2 text-nowrap" id="searchBtn6" tabindex="-1">e-Books</button></li>
                      <li class="nav-item"><button aria-controls="searchPanel7" aria-expanded="false" class="Accordion-trigger p-2" id="searchBtn7" tabindex="-1">Videos</button></li>
                    </ul>
                <!--</div>-->
             </nav>
          </div>
          <div class="card-body p-0">
             <div aria-labelledby="searchBtn1" class="Accordion-panel" id="searchPanel1" role="region">';
                $html .= $this->getCatalogueForm();
             $html .= '</div>
             <div aria-labelledby="searchBtn2" class="Accordion-panel" id="searchPanel2" role="region" hidden="">';
                $html .= $this->getReservesForm();
             $html .= '</div>
             <div aria-labelledby="searchBtn3" class="Accordion-panel" id="searchPanel3" role="region" hidden="">';
                $html .= $this->getDatabasesForm();
             $html .= '</div>
             <div aria-labelledby="searchBtn4" class="Accordion-panel" id="searchPanel4" role="region" hidden="">';
                $html .= $this->getJournalsForm();
             $html .= '</div>
             <div aria-labelledby="searchBtn5" class="Accordion-panel" id="searchPanel5" role="region" hidden="">';
                $html .= $this->getEncyclopediasForm();
             $html .= '</div>
             <div aria-labelledby="searchBtn6" class="Accordion-panel" id="searchPanel6" role="region" hidden="">';
                $html .= $this->getEbooksForm();
             $html .= '</div>
             <div aria-labelledby="searchBtn7" class="Accordion-panel" id="searchPanel7" role="region" hidden="">';
                $html .= $this->getVideosForm();
             $html .= '</div>
          </div>
        </div>
        </div>
        <div id="ask-us" class="d-none d-lg-block bg-askus p-2">
           <div class="d-flex mt-3">
            <div class="flex-grow-1"><h2 class="text-black border-bottom-0">Ask Us</h2></div>
            <div><i class="fas fa-comments fa-3x"></i></div>
           </div>
           <form class="pt-2 pb-1">
            <div class="form-group">
              <label for="chat-askus" class="sr-only">Email address</label>
              <input id="chat-askus" type="text" class="form-control" placeholder="Chat is open">
              <p id="ask-us-help" class="mt-3 text-center">
                <a href="#">
                  <span>Phone,</span>    
                  <span>Text,</span>  
                  <span>Email,</span>  
                  <span>In-Person</span>  
                </a>
              </p>
            </div>
           </form>
</form>
           
        </div>
      </div>';

    return [
      '#children' => $html,
      '#attached' => [
        'library' => [
          'lib_core/unblibtabs',
          'lib_core/lib-chosen',
          'lib_core/chosen-bootstrap',
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
      '<form action="//web.lib.unb.ca/reserves/index.php/quickSearch" id="searchReserves" method="post">
        <div class="form-group">
          <div class="form-row">
            <div class="col-md-7 mb-2">
              <label class="sr-only" for="keywords">
                Search for:
              </label>
              <div class="input-group">              
                <input class="form-control" id="keywords" name="keywords" placeholder="Search by instructor, course name, or course number" type="search" value="" required>
              </div>
            </div>
            <div class="col-md-4 input-group flex-nowrap mb-2">
              <label class="sr-only" for="semester">
                Course Semester
              </label>';
    $form_reserves .= $this->getReservesSemesters();
    $form_reserves .= '</div>
            <div class="col-md-1 mb-2">
              <button class="btn btn-primary px-5 px-md-3" id="searchReservesSubmit" type="submit">GO</button>
            </div>
          </div>
        </div>
      </form>
      <div class="px-2">
        <a href="//web.lib.unb.ca/reserves/index.php?h=1">Login to My UNB Reserves</a>
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
      '<form accept-charset="UTF-8" action="/worldcat-search-helper" id="home_WCD" method="post" name="wcfw">
        <div class="form-group">
          <fieldset>
            <div class="form-row mb-2 ml-1">
              <legend class="custom-legend mb-1 mr-3">
                Search UNB WorldCat:
              </legend>
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0 font-weight-bold">
                <input checked="checked" class="custom-control-input" id="scope_UNBLibraries_WCD" name="scope" type="radio" value="wz:66413">
                <label class="custom-control-label" for="scope_UNBLibraries_WCD"><span class="sr-only">Search </span>UNB Libraries</label>
              </div>
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0 font-weight-bold">
                <input class="custom-control-input" id="scope_worldwide_WCD" name="scope" type="radio" value="">
                <label class="custom-control-label" for="scope_worldwide_WCD"><span class="sr-only">Search </span>Libraries Worldwide</label>
              </div>
            </div>
          </fieldset>
          <div class="form-row">
            <div class="col-md-7 mb-2">
              <label class="sr-only" for="queryString_WCD">
                Search for:
              </label>
              <div class="form-group">
                <input class="form-control" id="queryString_WCD" name="queryString" placeholder="Search books, articles, and more" type="search" required>
              </div>
            </div>
            <div class="col-md-4 mb-2 form-group flex-nowrap">
              <label class="sr-only" for="searchIndex_WCD">
                Search index
              </label>
              <div class="form-group">
                <select class="custom-select form-control" id="searchIndex_WCD" name="searchIndex">
                  <option value="kw">keyword</option>
                  <option value="ti">title</option>
                  <option value="au">author</option>
                  <option value="nu">call number</option>
                  <option value="tj">journal title</option>
                  <option value="su">subject</option>
                </select>
              </div>
            </div>
            <div class="col-md-1">
              <button class="btn btn-primary px-5 px-md-3" id="search_WCD" title="Search" type="submit">GO</button>
            </div>
          </div>
        </div>
      </form>
      <ul class="list inline m-0 p-0">
        <li class="list-inline-item mr-3">
            <a href="//unb.on.worldcat.org/advancedsearch">Advanced Search</a>
        </li>    
        <li class="list-inline-item mr-3">
            <a href="https://lib.unb.ca/about/loc_call">What Am I Searching?</a>
        </li>
        <li class="list-inline-item">    
            <a href="/worldcat/unb-worldcat-frequently-asked-questions" title="Using WorldCat Discovery">Help</a>
        </li>
      </ul>';

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
            <label class="ml-2" for="database-subjects">
              Browse databases by subject
            </label>
          </div>
          <div class="form-row">
            <div class="col-md-10 input-group flex-nowrap mb-2">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  <i class="fas fa-database"></i>
                </span>
              </div>';
    $subject_form .= $this->getDatabasesSubjects();
    $subject_form .= '</div>
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
              Browse for databases by title
            </label>
          </div>
          <div class="form-row">
            <div class="col-md-10 input-group input-group-lg flex-nowrap mb-2">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  <i class="fas fa-database"></i>
                </span>
              </div>';
    $title_form .= $this->getDatabasesTitles();
    $title_form .= '</div>
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
      '' => '- Please choose an option -',
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
        'class' => [
          'custom-chosen-select',
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
      '<form action="https://web.lib.unb.ca/eresources/index.php" id="search_results_journals" method="get">
        <input id="sub" name="sub" type="hidden" value="journals">
        <div class="form-group mt-1">
          <div class="form-row font-weight-bold ml-1">
          <fieldset>
              <legend class="sr-only">Search options</legend>
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0">
                <input checked="checked" class="custom-control-input" id="searchtype_every_journal" name="searchtype" type="radio" value="every_word">
                <label class="custom-control-label" for="searchtype_every_journal">
                  Word(s) in title
                </label>
              </div>
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0">
                <input class="custom-control-input" name="searchtype" id="searchtype_browse_journal" type="radio" value="browse">
                <label class="custom-control-label" for="searchtype_browse_journal">
                  Starts with
                </label>
              </div>
              <div class="custom-control custom-radio custom-control-inline mb-2 mb-lg-0">
                <input class="custom-control-input" name="searchtype" id="searchtype_exact_journal" type="radio" value="exact">
                <label class="custom-control-label" for="searchtype_exact_journal">
                  </span>Exact
                </label>
              </div>
            </fieldset>
          </div>
        </div>
        <div class="form-group">
          <div class="form-row">        
            <label class="sr-only" for="title_journal">
              Title
            </label>
            <div class="col-md-10 mb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">
                    <i class="fas fa-search"></i>
                  </div>
                </div>
                <input class="form-control" id="title_journal" name="title" placeholder="Search for journal and newspaper titles" type="search" required>
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
