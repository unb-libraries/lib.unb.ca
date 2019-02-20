<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides the UNB Libraries Megamenu Header Block.
 *
 * @Block(
 *   id = "megamenu_block",
 *   admin_label = @Translation("UNB Libraries megamenu header block"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class MegamenuBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' =>
        '<div class="row">
          <div class="col-12">
            <nav class="navbar navbar-expand-lg p-0">
              <div class="d-flex col-md-12 col-lg-4 justify-content-center"><a class="unblib-logo" href="https://lib.unb.ca"><img alt="University of New Brunswick; established 1785" class="img-fluid" src="/themes/custom/unb_lib_theme/dist/img/unb-libraries-red-black.png" /></a></div>
              <div class="col-xs-4 navbar-toggler"><a aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation" class="btn btn-danger" data-target="#navbar" data-toggle="collapse" href="#" role="button">Menu <span class="fas fa-bars"></span> </a></div>
              <div class="col-xs-4 navbar-toggler quicklinks-toggler"><a aria-controls="navbar" aria-expanded="false" aria-label="Toggle QuickLinks" class="btn btn-danger" data-target="#quicklinks" data-toggle="collapse" href="#" role="button">QuickLinks <span class="fas fa-link"></span> </a></div>
              <div class="col-xs-4 navbar-toggler hours-toggler"><a aria-controls="navbar" aria-expanded="false" aria-label="Toggle Hours" class="btn btn-danger" data-target="#hours" data-toggle="collapse" href="#" role="button">Hours <span class="fas fa-clock"></span> </a></div>
              <div class="col-xs-4 navbar-toggler"><a class="btn btn-danger" href="https://lib.unb.ca/help/ask.php">AskUs <span class="fas fa-comments"></span> </a></div>
        
              <div class="collapse navbar-collapse justify-content-center" id="navbar">
                <ul class="navbar-nav" role="menubar">
                  <li class="nav-item dropdown megamenu-li"><a aria-expanded="false" aria-haspopup=" true" class="nav-link p-4" data-toggle="dropdown" href="#" id="about" role="button">About</a>
                    <div aria-labelledby="about" class="dropdown-menu megamenu m-0 p-3">
                      <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                          <h3>General</h3>
                          <ul>
                            <li><a class="nav-link" href="#">Contact Us</a></li>
                            <li><a class="nav-link" href="#">Find Us</a></li>
                            <li><a class="nav-link" href="#">Staff and Departments</a></li>
                            <li><a class="nav-link" href="#">Hours</a></li>
                            <li><a class="nav-link" href="#">Policies</a></li>
                            <li><a class="nav-link" href="#">Student Employment</a></li>
                          </ul>
                        </div>
            
                        <div class="col-sm-12 col-md-6 col-lg-4">
                          <h3>Libraries</h3>
                          <ul>
                            <li><a class="nav-link" href="#">Harriet Irving</a></li>
                            <li><a class="nav-link" href="#">Science &amp; Forestry</a></li>
                            <li><a class="nav-link" href="#">Engineering &amp; Computer Science</a></li>
                            <li><a class="nav-link" href="#">Hans W. Klohn Commons</a></li>
                            <li><a class="nav-link" href="#">Gerard V. La Forest Law</a></li>
                          </ul>
                        </div>
            
                        <div class="col-sm-12 col-md-6 col-lg-4">
                          <h3>Departments</h3>
                          <ul>
                            <li><a class="nav-link" href="#">Archives &amp; Special Collections</a></li>
                            <li><a class="nav-link" href="#">Centre for Digital Scholarship</a></li>
                            <li><a class="nav-link" href="#">Microforms</a></li>
                            <li><a class="nav-link" href="#">Government Documents, Data &amp; Maps</a></li>
                            <li><a class="nav-link" href="#">…&nbsp;more</a></li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </li>
                  <li class="nav-item dropdown megamenu-li"><a aria-expanded="false" aria-haspopup="true" class="nav-link p-4" data-toggle="dropdown" href="#" id="resources" role="button">Resources</a>
                    <div aria-labelledby="resources" class="dropdown-menu megamenu m-0 p-3">
                      <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                          <h3>Resources</h3>
                          <ul>
                            <li><a class="nav-link" href="#">e-Resources</a></li>
                            <li><a class="nav-link ml-3" href="#">Article Databases</a></li>
                            <li><a class="nav-link ml-3" href="#">e-Encyclopedias, etc.</a></li>
                            <li><a class="nav-link ml-3" href="#">Journals &amp; Newspapers</a></li>
                            <li><a class="nav-link" href="#">e-Books</a></li>
                            <li><a class="nav-link" href="#">Videos</a></li>
                            <li><a class="nav-link" href="#">Current Trials</a></li>
                            <li><a class="nav-link" href="#">Zotero</a></li>
                            <li><a class="nav-link" href="#">Mobile Resources for Academics</a></li>
                          </ul>
                        </div>
    
                        <div class="col-sm-12 col-md-6 col-lg-4">
                          <h3>Catalogues</h3>
                          <ul>
                            <li><a class="nav-link" href="#">UNB WorldCat</a></li>
                            <li><a class="nav-link" href="#">WorldCat FirstSearch</a></li>
                            <li><a class="nav-link" href="#"><span lang="fr">LAC Voilà</span> <span style="color:#fc0"><i class="fas fa-star"></i> NEW</span></a></li>
                            <li><a class="nav-link" href="#">New Brunswick Public Library</a></li>
                          </ul>
    
                          <h3 class="mt-4">Reserves</h3>
                          <ul>
                            <li><a class="nav-link" href="#">Search for Course reserves</a></li>
                          </ul>
    
                          <h3 class="mt-4">Subject &amp; Course Guides</h3>
                          <ul>
                            <li><a class="nav-link" href="#">Research by subject or course</a></li>
                          </ul>
                        </div>
    
                        <div class="col-sm-12 col-md-6 col-lg-4">
                          <h3>Notable Collections</h3>
                          <ul>
                            <li><a class="nav-link" href="#">Archives &amp; Special Collections</a></li>
                            <li><a class="nav-link" href="#">Eileen Wallace Children\'s Literature</a></li>
                            <li><a class="nav-link" href="#">UNB Scholar Research Repository</a></li>
                            <li><a class="nav-link" href="#">Film/Sound/Image Collections</a></li>
                            <li><a class="nav-link" href="#">Government Documents, Data &amp; Maps</a></li>
                            <li><a class="nav-link" href="#">Loyalist Collection</a></li>
                            <li><a class="nav-link" href="#">Science Fiction &amp; Fantasy Collection</a></li>
                            <li><a class="nav-link" href="#">UNB Theses &amp; Dissertations</a></li>
                            <li><a class="nav-link" href="#">…&nbsp;more</a></li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </li>
                  <li class="nav-item dropdown megamenu-li"><a aria-expanded="false" aria-haspopup="true" class="nav-link p-4" data-toggle="dropdown" href="#" id="services" role="button">Services</a>
                    <div aria-labelledby="services" class="dropdown-menu megamenu m-0 p-3">
                      <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                          <h3>Help with</h3>
                          <ul>
                            <li><a class="nav-link" href="#">Research</a></li>
                            <li><a class="nav-link" href="#">Writing Help</a></li>
                            <li><a class="nav-link" href="#">Technical Issues</a></li>
                          </ul>
        
                          <h3 class="mt-4">Request Forms</h3>
                          <ul>
                            <li><a class="nav-link" href="#">Document Delivery</a></li>
                            <li><a class="nav-link" href="#">Material Retrieval Form</a></li>
                          </ul>
                        </div>
        
                        <div class="col-sm-12 col-md-6 col-lg-4">
                          <h3>Information about …</h3>
                          <ul>
                            <li><a class="nav-link" href="#">Copyright</a></li>
                            <li><a class="nav-link" href="#">Photocopying, Printing &amp; Scanning</a></li>
                            <li><a class="nav-link" href="#">Group Study Rooms</a></li>
                            <li><a class="nav-link" href="#">GIS</a></li>
                            <li><a class="nav-link" href="#">Data Services</a></li>
                            <li><a class="nav-link" href="#">McNair Learning Commons</a></li>
                          </ul>
                        </div>
        
                        <div class="col-sm-12 col-md-6 col-lg-4">
                          <h3>Services for …</h3>
                          <ul>
                            <li><a class="nav-link" href="#">Persons with Disabilities</a></li>
                            <li><a class="nav-link" href="#">Distance Education</a></li>
                            <li><a class="nav-link" href="#">Faculty</a></li>
                            <li><a class="nav-link" href="#">Graduate Students</a></li>
                            <li><a class="nav-link" href="#">Alumni</a></li>
                            <li><a class="nav-link" href="#">Community</a></li>
                            <li><a class="nav-link" href="#">STU Students</a></li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </li>
                  <li class="nav-item dropdown megamenu-li"><a aria-expanded="false" aria-haspopup="true" class="nav-link p-4" data-toggle="dropdown" href="#" id="faculty" role="button">Faculty</a>
                    <div aria-labelledby="faculty" class="dropdown-menu megamenu m-0 p-3">
                      <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                          <h3>Teaching</h3>
                          <ul>
                            <li><a class="nav-link" href="#">Working with Copyright &amp; Licensed Resources</a></li>
                            <li><a class="nav-link" href="#">Course Reserves</a></li>
                            <li><a class="nav-link" href="#">Library Instruction Services</a></li>
                            <li><a class="nav-link" href="#">Instruction Facilities</a></li>
                            <li><a class="nav-link" href="#">Teaching Resources</a></li>
                            <li><a class="nav-link" href="#">Reserve a DVD/Video</a></li>
                          </ul>
                        </div>
    
                        <div class="col-sm-12 col-md-6 col-lg-4">
                          <h3>Research</h3>
                          <ul>
                            <li><a class="nav-link" href="#">Citation Metrics and Scholarly Profiles</a></li>
                            <li><a class="nav-link" href="#">Research Data Management</a></li>
                            <li><a class="nav-link" href="#">Supporting Open Access</a></li>
                            <li><a class="nav-link" href="#">Deposit to UNB Scholar</a></li>
                            <li><a class="nav-link" href="#">Digital Publishing Services</a></li>
                          </ul>
                        </div>
    
                        <div class="col-sm-12 col-md-6 col-lg-4">
                          <h3>Collections Development</h3>
                          <ul>
                            <li><a class="nav-link" href="#">News, Reports and Policies</a></li>
                          </ul>
    
                          <h3 class="mt-4">Subject Librarians</h3>
                          <ul>
                            <li><a class="nav-link" href="#">Directory of Liaisons by Subject Area</a></li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </li>
                  <li class="nav-item dropdown megamenu-li"><a aria-expanded="false" aria-haspopup="true" class="nav-link p-4" data-toggle="dropdown" href="#" id="help" role="button">Help</a>
                    <div aria-labelledby="help" class="dropdown-menu megamenu m-0 p-3">
                      <div class="row">
                        <div class="col-sm-12 col-md-6">
                          <h3>Research &amp; Writing</h3>
                          <ul>
                            <li><a class="nav-link" href="#">Ask Us</a></li>
                            <li><a class="nav-link" href="#">Research Help</a></li>
                            <li><a class="nav-link" href="#">Writing Help</a></li>
                            <li><a class="nav-link" href="#">Guides &amp; Workshops</a></li>
                            <li><a class="nav-link" href="#">Video Quick Tips</a></li>
                            <li><a class="nav-link" href="#">Citing Your Sources</a></li>
                            <li><a class="nav-link" href="#">Accessibility Support</a></li>
                          </ul>
                        </div>
    
                        <div class="col-sm-12 col-md-6">
                          <h3>Technical Support</h3>
                          <ul>
                            <li><a class="nav-link" href="#">Off-campus Access: Proxy and VPN</a></li>
                            <li><a class="nav-link" href="#">Systems and Services Status</a></li>
                            <li><a class="nav-link" href="#">Submit a Trouble Ticket</a></li>
                            <li><a class="nav-link" href="#">Website Accessibility</a></li>
                            <li><a class="nav-link" href="#">Test your browser/computer settings</a></li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </nav>
          </div>
        </div>',
    ];
  }

}
