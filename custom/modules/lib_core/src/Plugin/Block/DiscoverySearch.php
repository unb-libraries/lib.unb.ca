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
    $html = '
    <div class="tabs-theme-dark">
    <ul id="tabs" class="flex-fill nav nav-tabs" role="tablist">
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
               Article Databases
            </a>
        </li>
        <li class="nav-item">
            <a id="tab-journals" href="#pane-journals" class="nav-link" data-toggle="tab" role="tab">
                Journals &amp; Newspapers
            </a>
        </li>
        <li class="nav-item">
            <a id="tab-encyclopedias" href="#pane-encyclopedias" class="nav-link" data-toggle="tab" role="tab">
                e-Encyclopedias, etc.
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
    <div id="discovery-search" class="d-flex">
    <div id="content" class="flex-grow-1 tab-content" role="tablist">
        <div id="pane-reserves" class="flex-grow-1 card tab-pane my-1 fade show active" role="tabpanel" aria-labelledby="tab-reserves">
            <div class="card-header p-0" role="tab" id="heading-reserves">
                <h3 class="m-0">
                    <a class="nav-link mx-1 p-2" data-toggle="collapse" href="#collapse-reserves" aria-expanded="true" aria-controls="collapse-reserves">
                        Reserves
                    </a>
                </h5>
            </div>
            <div id="collapse-reserves" class="collapse show" data-parent="#content" role="tabpanel" aria-labelledby="heading-reserves">
                <div class="card-body">
                    <form action="/core/action/process_reserves_search.php" method="get" id="searchReserves">
                        <p class="padHeight">
                            <label for="keywords"><strong>Search by instructor, course name or course number:</strong></label><br>
                            <input type="search" id="keywords" name="keywords" value="" placeholder="Enter keywords">
                            <label for="semester" class="hide">Course Semester</label>
                            <select name="semester" id="semester">
                                <option value="">All semesters</option>
                                <option value="2019WI" selected="selected">Winter 2019</option>
                                <option value="2018FA">Fall 2018</option>
                                <option value="2018FY">Full Year 2018</option>
                                <option value="2018SM">Summer 2018</option>
                                <option value="2018WI">Winter 2018</option>
                                <option value="2017FY">Full Year 2017</option>
                                <option value="2017FA">Fall 2017</option>
                                <option value="2017SM">Summer 2017</option>
                                <option value="2017WI">Winter 2017</option>
                            </select>
                            <input type="submit" value="GO" class="btn btn-inverse" id="searchReservesSubmit" title="GO">
                        </p>
                        <p style="padding:10px 0 0 5px;"> <a href="/reserves/index.php?h=1"><i class="fa fa-user"></i> Login to My UNB Reserves</a></p>
      				</form>
                </div>
            </div>
        </div>

        <div id="pane-catalogue" class="flex-grow-1 card tab-pane my-1 fade" role="tabpanel" aria-labelledby="tab-catalogue">
            <div class="card-header p-0" role="tab" id="heading-catalogue">
                <h3 class="m-0">
                    <a class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-catalogue" aria-expanded="false" aria-controls="collapse-catalogue">
                        Catalogue
                    </a>
                </h5>
            </div>
            <div id="collapse-catalogue" class="collapse" data-parent="#content" role="tabpanel" aria-labelledby="heading-catalogue">
                <div class="card-body">
                    <form id="home_WCD" name="wcfw" method="post" accept-charset="UTF-8" action="/core/inc-2015/UNB-WorldCat-Discovery-search.php">
                        <p class="padHeight">
                            <strong>Search UNB WorldCat:</strong>&nbsp; &nbsp;
                            <label for="scope_UNBLibraries_WCD"><input type="radio" checked="checked" value="wz:66413" name="scope" id="scope_UNBLibraries_WCD" class="radioSelect">
                                UNB Libraries
                            </label>
                            &nbsp;
                            <label for="scope_worldwide_WCD"><input type="radio" value="" name="scope" id="scope_worldwide_WCD" class="radioSelect">
                                Libraries Worldwide
                            </label>
                        </p>
                        <p>
                            <label for="queryString_WCD" class="hidden">
                                Search for:
                            </label>
                            <input type="search" name="queryString" id="queryString_WCD" placeholder="Enter search terms">
                            <label for="searchIndex_WCD" class="hide">
                                Search index
                            </label>
                            <select name="searchIndex" id="searchIndex_WCD" class="pulldown" style="width:130px;">
                                <option value="kw">keyword</option>
                                <option value="ti">title</option>
                                <option value="au">author</option>
                                <option value="nu">call number</option>
                                <option value="tj">journal title</option>
                                <option value="su">subject</option>
                            </select>
                            <input type="submit" value="GO" class="btn btn-inverse" id="search_WCD" title="Search">
                        </p>
                        <p style="padding:10px 0 0 5px;">
                            <a href="http://unb.on.worldcat.org/advancedsearch">Advanced Search</a> |
                            <a href="/about/loc_call/">Locations Guide</a> |
                            <a href="/worldcat/FAQs.php" title="Using WorldCat Discovery"><i class="fa fa-question-circle"></i> Help</a>
                        </p>  		
		            </form>
                </div>
            </div>
        </div>

        <div id="pane-databases" class="flex-grow-1 card tab-pane my-1 fade" role="tabpanel" aria-labelledby="tab-databases">
            <div class="card-header p-0" role="tab" id="heading-databases">
                <h3 class="m-0">
                    <a class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-databases" aria-expanded="false" aria-controls="collapse-databases">
                       Article Databases
                    </a>
                </h5>
            </div>
            <div id="collapse-databases" class="collapse" role="tabpanel" data-parent="#content" aria-labelledby="heading-databases">
                <div class="card-body">
                    <form class="categorySelect">
                        <label for="category">
                            Browse databases by subject
                        </label>
                        <div>
                            <select name="category"> 
                                <option value="">Select a subject...</option>
                                <option value="anthropology">Anthropology</option>
                                <option value="biology">Biology</option><option value="business">Business Administration</option>
                                <option value="chemistry">Chemistry</option>
                                <option value="classics">Classics and Ancient History</option>
                                <option value="ics">Communication Studies</option>
                                <option value="computerscience">Computer Science</option>
                                <option value="criminology">Criminology</option>
                                <option value="earth-sciences">Earth Sciences</option>
                                <option value="economics">Economics</option>
                                <option value="education">Education</option>
                                <option value="engineering">Engineering</option><option value="english">English</option>
                                <option value="entrepreneurship">Entrepreneurship</option>
                                <option value="familyviolence">Family Violence Issues</option>
                                <option value="film">Film Studies</option><option value="finearts">Fine Arts</option>
                                <option value="forestry">Forestry &amp; Environmental Management</option>
                                <option value="french">French</option><option value="womensstudies">Gender and Women\'s Studies</option>
                                <option value="german">German Literature</option>
                                <option value="gerontology">Gerontology</option><option value="history">History</option>
                                <option value="development">International Development Studies</option>
                                <option value="journalism">Journalism &amp; Communications</option>
                                <option value="kinesiology">Kinesiology</option>
                                <option value="law">Law</option><option value="math">Mathematics</option>
                                <option value="media">Media Arts and Culture</option>
                                <option value="music">Music</option><option value="nativestudies">Native Studies</option>
                                <option value="nursing">Nursing &amp; Health Sciences</option>
                                <option value="philosophy">Philosophy</option><option value="physics">Physics</option>
                                <option value="politicalscience">Political Science</option>
                                <option value="psychology">Psychology</option>
                                <option value="religiousstudies">Religious Studies</option>
                                <option value="sts">Science &amp; Technology Studies</option>
                                <option value="socialwork">Social Work</option>
                                <option value="sociology">Sociology</option>
                                <option value="spanish">Spanish Literature</option>
                            </select>
                            <input type="submit" class="btn btn-inverse" value="GO">
                        </div>
                    </form>
                    <form id="title_results" method="get" action="/eresources/index.php">
                        <p>
                            <label for="databaseID" class="searchOptionsDivider">
                                <span>OR</span> &nbsp;<strong>Browse for databases by title</strong>
                            </label>
                        </p>
                        <select name="id" id="databaseID">
                            <option value="">Choose a database title</option>
                            <opt  ion value="3606">17th and 18th century Nichols newspapers collection</option>
                            <option value="3548">17th-18th century Burney Collection newspapers</option>
                            <option value="3250">ABI/INFORM Complete (ProQuest)</option>
                            <option value="456">Abstracts in Anthropology Online</option>
                            <option value="556">Abstracts in Social Gerontology</option>
                            <option value="1">Academic Search Premier</option>
                            <option value="40">Access UN</option>
                            <option value="334">AGIS Plus Text</option>
                            <option value="10">Agricola (National Agricultural Library Catalog)</option>
                            <option value="565">America: History and Life (EBSCO)</option>
                            <option value="3426">American Antiquarian Society (AAS) Historical Periodicals Collection...</option>
                            <option value="83">American Chemical Society (ACS)</option>
                            <option value="3142">American Economic Association</option>
                            <option value="421">American Film Scripts Online (AFSO)</option>
                            <option value="167">American Mathematical Society</option>
                            <option value="165">American Physical Society</option>
                            <option value="319">American Society for Microbiology (ASM)</option>
                            <option value="113">American Society of Mechanical Engineers (ASME)</option>
                            <option value="3530">America’s Historical Newspapers: Early American Newspapers</option>
                            <option value="327">Année philologique</option>
                            <option value="398">Annual Bibliography of English Language and Literature (ABELL)</option>
                            <option value="307">Annual Reviews</option>
                            <option value="335">Anthropology Plus</option>
                            <option value="1710">AnthroSource</option>
                            <option value="32">Aquatic Sciences and Fisheries Abstracts (ASFA)</option>
                            <option value="23">Art Index</option>
                            <option value="450">ARTstor</option>
                            <option value="1826">arXiv.org</option>
                            <option value="114">ASCE Library eJournals (American Society of Civil Engineers)</option>
                            <option value="422">Asian American Drama</option>
                            <option value="95">Association for Computing Machinery (ACM) Digital Library</option>
                            <option value="3365">ASTM Compass - International Standards &amp; Digital Library</option>
                            <option value="347">ATLA Religion Database</option>
                            <option value="3605">BCC Research Academic Library</option>
                            <option value="344">Bepress Legal Repository</option>
                            <option value="22">Bibliography of Native North Americans</option>
                            <option value="543">Biological Abstracts (now in BIOSIS Citation Index)</option>
                            <option value="348">BioMed Central (Open Access Journals)</option>
                            <option value="1706">BIOSIS Citation Index (Including Biological Abstracts)</option>
                            <option value="423">Black Drama</option>
                            <option value="424">Black Thought and Culture</option>
                            <option value="425">British and Irish Women\'s Letters and Diaries</option>
                            <option value="3333">BrowZine</option>
                            <option value="2">Business Source Ultimate (formerly Premier version)</option>
                            <option value="542">CAB Direct</option>
                            <option value="158">Cambridge Core (eBooks &amp; eJournals)</option>
                            <option value="311">Canadian Bar Association</option>
                            <option value="26">Canadian Business and Current Affairs (CBCA) Business (ProQuest)</option>
                            <option value="3055">Canadian Business and Current Affairs (CBCA) Complete (ProQuest)</option>
                            <option value="320">Canadian Business and Current Affairs (CBCA) Current Events (ProQuest...</option>
                            <option value="164">Canadian Business and Current Affairs (CBCA) Education (ProQuest)</option>
                            <option value="27">Canadian Business and Current Affairs (CBCA) Reference (ProQuest)</option>
                            <option value="451">Canadian Newspapers FULLTEXT (Infomart)</option>
                            <option value="368">Canadian Patent Reporter Plus</option>
                            <option value="184">Canadian Periodicals Index Quarterly (CPI.Q)</option>
                            <option value="29">Canadian Research Index (CRI)</option>
                            <option value="596">CARD Online (CARDonline)</option>
                            <option value="313">Centre for Digital Scholarship eJournals (UNB CDS)</option>
                            <option value="511">Charter of Rights in Litigation: Direction from the Supreme Court...</option>
                            <option value="1471">Chemical Abstracts (SciFinder Web)</option>
                            <option value="174">Children\'s Literature Comprehensive Database (CLCD)</option>
                            <option value="577">China: Trade, Politics and Culture, 1793-1980 (Adam Matthew Digital...</option>
                            <option value="186">CHRR (Canadian Human Rights Reporter) Online</option>
                            <option value="161">CINAHL with Full Text (Cumulative Index to Nursing &amp; Allied Health...</option>
                            <option value="594">Classical Scores Library (Music Online)</option>
                            <option value="3509">Cochrane Clinical Answers (CCAs - Wiley)</option>
                            <option value="459">Cochrane Library</option>
                            <option value="544">Communication &amp; Mass Media Complete (CMMC)</option>
                            <option value="326">Computer Source Index (formerly Computer Science Index)</option>
                            <option value="1760">Criminal Justice Abstracts - EBSCO</option>
                            <option value="125">Current Index to Statistics</option>
                            <option value="3262">D&amp;B Hoovers (formerly Hoover\'s Online)</option>
                            <option value="440">Database of Abstracts of Reviews of Effects</option>
                            <option value="3437">De Gruyter Journals</option>
                            <option value="578">Defining Gender, 1450-1910 (Adam Matthew Digital)</option>
                            <option value="557">Digital National Security Archive (DNSA - ProQuest)</option>
                            <option value="345">Directory of Open Access Journals (DOAJ)</option>
                            <option value="44">Dissertations &amp; Theses (ProQuest PQDT: formerly Digital Dissertations...</option>
                            <option value="373">Duke University Press</option>
                            <option value="426">Early Encounters in North America</option>
                            <option value="173">Early English Books Online (EEBO)</option>
                            <option value="3578">Earth, Atmospheric, &amp; Aquatic Science Database (ProQuest)</option>
                            <option value="8">Econlit</option>
                            <option value="521">eHRAF Archaeology</option>
                            <option value="53">eHRAF World Cultures</option>
                            <option value="506">Eighteenth Century Collections Online (ECCO)</option>
                            <option value="587">Eighteenth Century Journals (Adam Matthew Digital)</option>
                            <option value="3370">eMarketer Pro</option>
                            <option value="166">Emerald Journals</option>
                            <option value="579">Empire Online (Adam Matthew Digital)</option>
                            <option value="3403">Engineering Index Backfile (EI - Engineering Village)</option>
                            <option value="9">ERIC - EBSCO</option>
                            <option value="33">ERIC - ProQuest</option>
                            <option value="315">Érudit</option>
                            <option value="391">ESTC  (English Short Title Catalogue)</option>
                            <option value="1692">EThOS (Electronic Theses Online Service : UK Theses)</option>
                            <option value="1786">European Views of the Americas: 1493 to 1750</option>
                            <option value="3232">Films on Demand</option>
                            <option value="3328">Financial times (FT.com)</option>
                            <option value="3327">First Research - Industry Profiles (Mergent Intellect)</option>
                            <option value="297">Forestscience (formerly Forestscience.info)</option>
                            <option value="3526">Frontier Life: Borderlands, Settlement &amp; Colonial Encounters (Adam...</option>
                            <option value="3405">Frost &amp; Sullivan</option>
                            <option value="18">GeoRef</option>
                            <option value="3547">Gerritsen Collection of Aletta H. Jacobs - Books &amp; Journals</option>
                            <option value="454">Google Scholar</option>
                            <option value="599">Health and Psychosocial Instruments (HaPI : EBSCO)</option>
                            <option value="405">HealthStar</option>
                            <option value="123">HeinOnline</option>
                            <option value="3166">HeinOnline - Canada Supreme Court Reports</option>
                            <option value="3164">HeinOnline - English Reports, Full Reprint (1220-1867)</option>
                            <option value="567">HeinOnline - Foreign &amp; International Law Resources Database</option>
                            <option value="3165">HeinOnline - Harvard Research in International Law</option>
                            <option value="1680">HeinOnline - Philip C. Jessup Library</option>
                            <option value="3290">HeinOnline - Revised Statutes of Canada</option>
                            <option value="3162">HeinOnline - Session Laws Library</option>
                            <option value="3562">HeinOnline - Slavery in America and the World: History, Culture &amp;...</option>
                            <option value="3163">HeinOnline - World Constitutions Illustrated</option>
                            <option value="131">HighWire Press</option>
                            <option value="380">Highwire Press (Open Access)</option>
                            <option value="566">Historical Abstracts (EBSCO)</option>
                            <option value="549">Human Kinetics Publishers, Inc</option>
                            <option value="1709">IBISWorld - US/Canadian &amp; Global Industry Reports</option>
                            <option value="1721">ICE Virtual Library (Institution of Civil Engineers)</option>
                            <option value="3300">ICLR.3</option>
                            <option value="272">IEEE Xplore Digital Library (Current &amp; Archive)</option>
                            <option value="3391">InCites Essential Science Indicators (ESI)</option>
                            <option value="1674">Index to Canadian Legal Literature (Book Reviews subset)</option>
                            <option value="1673">Index to Canadian Legal Literature (Journals and Texts subset)</option>
                            <option value="1794">Index to Legal Periodicals &amp; Books Full Text</option>
                            <option value="1793">Index to Legal Periodicals Retrospective</option>
                            <option value="316">Infomart (formerly FPinfomart.ca) </option>
                            <option value="1759">Informa Healthcare Journals</option>
                            <option value="3573">INFORMIT Indigenous Collection </option>
                            <option value="94">IngentaConnect</option>
                            <option value="365">INIS (International Nuclear Information Systems)</option>
                            <option value="462">Inspec (Engineering Village)</option>
                            <option value="84">Institute of Physics Publishing (IOP)</option>
                            <option value="3386">International Bibliography of the Social Sciences (IBSS - ProQuest...</option>
                            <option value="331">Iter - Gateway to the Middle Ages and Renaissance</option>
                            <option value="88">JSTOR Archival Collection</option>
                            <option value="3047">JSTOR Current Collection</option>
                            <option value="116">JustisOne</option>
                            <option value="3340">Kanopy</option>
                            <option value="367">LabourSource</option>
                            <option value="427">Latino Literature</option>
                            <option value="119">LegalTrac</option>
                            <option value="108">Leisure, Recreation and Tourism (see CAB Direct)</option>
                            <option value="409">Library, Information Science &amp; Technology Abstracts (LISTA)</option>
                            <option value="569">Literature Criticism Online</option>
                            <option value="463">Literature Online</option>
                            <option value="312">LLMC-Digital</option>
                            <option value="1817">London Review of Books</option>
                            <option value="400">Longwoods Publishing</option>
                            <option value="1489">Loyalist Collection (Harriet Irving Library), The</option>
                            <option value="1491">Lyell Collection - Geological Society of London</option>
                            <option value="3550">Maclean\'s Magazine Archive (EBSCOhost)</option>
                            <option value="341">Manas Media\'s World Law reform database</option>
                            <option value="3416">Market share reporter</option>
                            <option value="472">Martin\'s Online Criminal Code</option>
                            <option value="580">Mass Observation Online (Adam Matthew Digital)</option>
                            <option value="54">MathSciNet</option>
                            <option value="3060">Medcom Video Training Programs Collection</option>
                            <option value="582">Medieval Travel Writing (Adam Matthew Digital)</option>
                            <option value="163">Medline (1950 to Present)</option>
                            <option value="324">Medline (In-Process and Other Non-Indexed Citations)</option>
                            <option value="323">Medline (OldMedline - 1951 to 1965 )</option>
                            <option value="507">Mental Measurements Yearbook (MMY)</option>
                            <option value="3282">Mergent Archives</option>
                            <option value="3317">Mergent Intellect</option>
                            <option value="3059">Mergent Online</option>
                            <option value="3263">Million Dollar Database (D &amp; B/Mergent)</option>
                            <option value="499">MIT Press Journals</option>
                            <option value="325">Modern Language Association (MLA) International Bibliography &amp; Directory...</option>
                            <option value="31">Modern Language Association (MLA) International Bibliography &amp; Directory...</option>
                            <option value="332">MSDS plus CHEMINFO</option>
                            <option value="487">Nation archive</option>
                            <option value="180">National Council of Teachers of English (NCTE)</option>
                            <option value="89">National Research Council (NRC)</option>
                            <option value="3044">Naver News Library</option>
                            <option value="3425">Naxos Music Library (NML)</option>
                            <option value="3270">NFB Campus (National Film Board of Canada)</option>
                            <option value="3200">Nineteenth Century Collections Online (NCCO eBooks)</option>
                            <option value="428">North American Immigrant Letters, Diaries and Oral Histories</option>
                            <option value="429">North American Indian Thought and Culture</option>
                            <option value="430">North American Theatre Online</option>
                            <option value="431">North American Women\'s Drama</option>
                            <option value="432">North American Women\'s Letters and Diaries</option>
                            <option value="554">OECD iLibrary - Books, Papers and Statistics</option>
                            <option value="375">OnePetro (formerly SPE eLibrary)</option>
                            <option value="435">Oral History Online</option>
                            <option value="339">OSH References Databases</option>
                            <option value="127">Ovid Journals - Lippincott Williams &amp; Wilkins Nursing &amp; Health Professions...</option>
                            <option value="171">Oxford University Press Journals</option>
                            <option value="1496">Parliament Rolls of Medieval England (PROME), The</option>
                            <option value="3277">Passport GMID (Global Market Information Database)</option>
                            <option value="644">Past Masters (Intelex)</option>
                            <option value="586">Periodicals Archive Online (PAO - ProQuest)</option>
                            <option value="25">Philosopher\'s Index (ProQuest)</option>
                            <option value="538">PILOTS Database</option>
                            <option value="3612">PitchBook</option>
                            <option value="593">Portico (e-Journals Archive)</option>    
                            <option value="1485">PressReader - Worldwide Newspapers **ON-CAMPUS ONLY** (formerly Library...</option>
                            <option value="404">Project Euclid</option>
                            <option value="87">Project Muse</option>
                            <option value="492">ProQuest Historical Newspapers</option>
                            <option value="410">ProQuest Historical Newspapers: The Globe and Mail</option>
                            <option value="159">ProQuest Nursing &amp; Allied Health Source</option>
                            <option value="3387">ProQuest Political Science</option>
                            <option value="3385">ProQuest Politics Collection</option>
                            <option value="336">PsycARTICLES</option>
                            <option value="3291">PsycBOOKS</option>
                            <option value="548">PsychiatryOnline</option>
                            <option value="11">PsycINFO</option>
                            <option value="3253">PsycTESTS</option>
                            <option value="24">Public Affairs Information Service (PAIS) International and Archive...</option>
                            <option value="115">Publisher</option>
                            <option value="452">PubMed</option>
                            <option value="169">PubMed Central (Open Access)</option>
                            <option value="3383">Quebec Government Documents - BAnQ Open Access Collections</option>
                            <option value="471">Quicklaw</option>
                            <option value="85">Royal Society of Chemistry (RSC)</option>
                            <option value="338">RTECS (Registry of Toxic Effects of Chemical Substances)</option>
                            <option value="352">SAGE Journals Online</option>
                            <option value="77">Science Direct</option>
                            <option value="1468">SciFinder Web (Chemical Abstracts, CAS React, CAS Registry)</option>
                            <option value="126">Scitation Journals - AIP Publishing (formerly American Institute...</option>
                            <option value="397">Scopus</option>
                            <option value="172">Selected Free eJournals</option>
                            <option value="181">SIAM Journals Online</option>
                            <option value="583">Slavery, Abolition and Social Justice, 1490-2007 (Adam Matthew Digital...</option>
                            <option value="441">Smithsonian Global Sound® for Libraries</option>
                            <option value="68">Social Services Abstracts</option>
                            <option value="541">Social Work Abstracts</option>
                            <option value="3161">SocINDEX with Full Text</option>
                            <option value="35">Sociological Abstracts</option>
                            <option value="12">SportDiscus</option>
                            <option value="86">SpringerLink</option>
                            <option value="3440">Statista</option>
                            <option value="475">Taylor and Francis Online - eJournals</option>
                            <option value="597">Theatre in Video</option>
                            <option value="1715">Thesaurus Linguae Graecae (TLG)</option>
                            <option value="322">Theses Canada Portal</option>
                            <option value="473">Times Digital Archives</option>
                            <option value="3610">Times Literary Supplement Historical Archive (TLS)</option>
                            <option value="3620">Trench Journals and Unit Magazines of the First World War (ProQuest...</option>
                            <option value="1683">TRIS (Transportation Research Information Services)</option>
                            <option value="433">Twentieth Century North American Drama</option>
                            <option value="371">U.K. Parliamentary Papers (ProQuest)</option>
                            <option value="3382">U.S. Government Documents (Open Access)</option>
                            <option value="3624">UN iLibrary</option>
                            <option value="3523">UNB Libraries - Government Documents Online (Open Access)</option>
                            <option value="3380">UNB Libraries - SCI-FORF Open Access Collection</option>
                            <option value="3351">UNB Libraries - Selected Newspapers (Online)</option>
                            <option value="3452">Uniform Law Conference of Canada (ULCC)</option>
                            <option value="3418">University of Chicago Press Journals</option>
                            <option value="3491">UpToDate</option>
                            <option value="545">Violence &amp; Abuse Abstracts</option>
                            <option value="584">Vividata - Jan 2019 NOTE: UNAVAILABLE until February (formerly Print...</option>
                            <option value="455">Wildlife &amp; Ecology Studies Worldwide</option>
                            <option value="132">Wiley Online Library</option>
                            <option value="434">Women and Social Movements in the United States, 1600-2000 (Basic...</option>
                            <option value="340">Women\'s Studies International</option>
                            <option value="403">World Shakespeare Bibliography Online</option>
                            <option value="80">Worldwide Political Science Abstracts (ProQuest)</option>
                        </select>
                        <input type="hidden" name="sub" value="indexes">
                        <input type="submit" value="GO" class="btn btn-inverse"></p>
                        <p class="moreOptions">
                            <i class="fa fa-list-ul"></i>
                            <a href="/eresources/index.php?sub=indexes">More Search Options</a>
                        </p>                               
                    </form>
                </div>
            </div>
        </div>
        
        <div id="pane-journals" class="flex-grow-1 card tab-pane my-1 fade" role="tabpanel" aria-labelledby="tab-journals">
            <div class="card-header p-0" role="tab" id="heading-journals">
                <h3 class="m-0">
                    <a class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-journals" aria-expanded="false" aria-controls="collapse-journals">
                        Journals &amp; Newspapers
                    </a>
                </h5>
            </div>
            <div id="collapse-journals" class="collapse" role="tabpanel" data-parent="#content" aria-labelledby="heading-journals">
                <div class="card-body">
                    <form action="/eresources/index.php" method="get" id="search_results_journals">
                        <input type="hidden" name="sub" id="sub" value="journals">
                        <p class="padHeight">
                            <label for="searchtype_every_journal"><input type="radio" checked="checked" value="every_word" name="searchtype" id="searchtype_every_journal" class="radioSelect">
                                Word(s) in title
                            </label>
                            <label for="searchtype_browse_journal"><input type="radio" value="browse" name="searchtype" id="searchtype_browse_journal" class="radioSelect">
                                Starts with
                            </label>
                            <label for="searchtype_exact_journal"><input type="radio" value="exact" name="searchtype" id="searchtype_exact_journal" class="radioSelect">
                                Exact
                            </label>
                        </p>
                        <p>
                            <label for="title_journal" class="hide">
                                title
                            </label>
                            <input id="title_journal" name="title" type="search" placeholder="Search for journal and newspaper titles">
                            <input type="submit" value="GO" class="btn btn-inverse">
                        </p>
                        <p style="padding-left:5px;">
                            <a href="/eresources/index.php?sub=journals&amp;packages=Y">
                                Journal Packages
                            </a> |
                            <a href="/eresources/index.php?sub=journals&amp;browseNewsColl=Y&amp;limitResourceType=enewsColl">
                                Newspaper Packages
                            </a> |
                            <a href="/eresources/newspapers.php" title="Guide to finding newspapers at UNB Libraries">
                                <!--img src="//media.lib.unb.ca/core/img/question_mark.gif" alt="" style="position:relative;top:-3px;" width="17" height="16"/-->
                                <i class="fa fa-question-circle"></i> Newspaper Guide
                            </a>
                        </p>
                        <!--span style="text-align: center;padding:5px 10px;margin-bottom:1px;display:inline-block" class="alert alert-warning">
                            <i class="fa fa-lg fa-exclamation-triangle text-danger"></i>
                            <a href="/help/status.php" style="border:0;outline:0;">
                                <strong>eJournals and eBooks Searching Currently Unavailable</strong>
                                <i class="fa fa-chevron-right"></i>
                            </a>
                        </span-->
                        <p class="moreOptions">
                            <i class="fa fa-list-ul"></i>
                            <a href="/eresources/index.php?sub=journals">
                                More Search Options
                            </a>
                        </p>
      			    </form>
                </div>
            </div>
        </div>
        
        <div id="pane-encyclopedias" class="flex-grow-1 card tab-pane my-1 fade" role="tabpanel" aria-labelledby="tab-encyclopedias">
            <div class="card-header p-0" role="tab" id="heading-encyclopedias">
                <h3 class="m-0">
                    <a class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-encyclopedias" aria-expanded="false" aria-controls="collapse-encyclopedias">
                        e-Encyclopedias, etc.
                    </a>
                </h5>
            </div>
            <div id="collapse-encyclopedias" class="collapse" role="tabpanel" data-parent="#content" aria-labelledby="heading-encyclopedias">
                <div class="card-body">
                    <form action="/eresources/index.php" method="get" id="search_results_refmat">
		                <input type="hidden" name="sub" id="sub_refmat" value="refmat">
                        <small class="first">Search for Reference Materials by title:</small>
                        <p class="padHeight">
                            <label for="searchtype_every_refmat"><input type="radio" checked="checked" value="every_word" name="searchtype" id="searchtype_every_refmat" class="radioSelect">
                                Word(s) in title
                            </label>
                            <label for="searchtype_browse_refmat"><input type="radio" value="browse" name="searchtype" id="searchtype_browse_refmat" class="radioSelect">
                                Starts with
                            </label>
                            <label for="searchtype_exact_refmat"><input type="radio" value="exact" name="searchtype" id="searchtype_exact_refmat" class="radioSelect">
                                Exact
                            </label>
                        </p>
                        <p class="padHeight">
                            <label class="hide" for="title_refmat">
                                title
                            </label>
                            <input type="search" value="" name="title" id="title_refmat" placeholder="Search for encyclopedias, dictionaries, etc.">
                            <input type="submit" value="GO" class="btn btn-inverse">
                        </p>
                        <p style="padding-left:5px;" class="padHeight">
                            <a href="/eresources/refguide.php" title="Guide to finding Reference Materials at UNB Libraries">
                                <i class="fa fa-compass"></i> Reference Materials Guide
                            </a> |
                            <a href="https://guides.lib.unb.ca/guide/98">
                                Browse dictionaries
                            </a>
                        </p>
                        <p class="moreOptions">
                            <i class="fa fa-th-list" aria-hidden="true"></i>
                            <a href="/eresources/index.php?sub=refmat">
                                More Search Options
                            </a>
                        </p>
      			    </form>
                </div>
            </div>
        </div>
        
        <div id="pane-ebooks" class="flex-grow-1 card tab-pane my-1 fade" role="tabpanel" aria-labelledby="tab-ebooks">
            <div class="card-header p-0" role="tab" id="heading-ebooks">
                <h3 class="m-0">
                    <a class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-ebooks" aria-expanded="false" aria-controls="collapse-ebooks">
                        e-Books
                    </a>
                </h5>
            </div>
            <div id="collapse-ebooks" class="collapse" role="tabpanel" data-parent="#content" aria-labelledby="heading-ebooks">
                <div class="card-body">
                    <p><small class="discoverNote">Search our vast electronic book collections for titles suitable for your computer, tablet or eReader.</small></p>
                    <form action="/eresources/index.php" method="get" id="search_results_ebooks">
                        <input type="hidden" name="sub" id="sub_ebooks" value="ebooks">
                        <p class="padHeight">
                            <label for="searchtype_every_ebooks">
                                <input type="radio" checked="checked" value="every_word" name="searchtype" id="searchtype_every_ebooks" class="radioSelect">
                                 Word(s) in title
                            </label>
                            <label for="searchtype_exact_ebooks"><input type="radio" value="exact" name="searchtype" id="searchtype_exact_ebooks" class="radioSelect">
                                Exact title
                            </label>
                            <label for="searchtype_keyword_ebooks"><input type="radio" value="keyword" name="searchtype" id="searchtype_keyword_ebooks" class="radioSelect">
                                Keyword search (title, author, publisher&hellip;)
                            </label>
                            <br>
                            <label class="hide" for="title_ebooks">
                                title
                            </label>
                            <input type="search" value="" name="title" id="title_ebooks">
                            <input type="submit" value="GO" class="btn btn-inverse">
                        </p>
                        <p style="padding:10px 0 0 5px;">
                            <a href="/eresources/index.php?sub=ebooks">
                                <i class="fa fa-th-list"></i>
                                Browse e-Book Collections
                            </a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
        
        <div id="pane-videos" class="flex-grow-1 card tab-pane my-1 fade" role="tabpanel" aria-labelledby="tab-videos">
            <div class="card-header p-0" role="tab" id="heading-videos">
                <h3 class="m-0">
                    <a class="nav-link mx-1 p-2 collapsed" data-toggle="collapse" href="#collapse-videos" aria-expanded="false" aria-controls="collapse-videos">
                        Videos
                    </a>
                </h5>
            </div>
            <div id="collapse-videos" class="collapse" role="tabpanel" data-parent="#content" aria-labelledby="heading-videos">
                <div class="card-body">
                    <p><small class="discoverNote">Search across our online video collections.</small></p>
                    <form action="/eresources/index.php" method="get" id="search_results_video">
                        <input type="hidden" name="sub" id="sub_video" value="video">
                        <p class="padHeight">
                            <label for="searchtype_every_video"><input type="radio" checked="checked" value="every_word" name="searchtype" id="searchtype_every_video" class="radioSelect">
                                Word(s) in title
                            </label>
                            <label for="searchtype_browse_video"><input type="radio" value="browse" name="searchtype" id="searchtype_browse_video" class="radioSelect">
                                Title starts with
                            </label>
                            <label for="searchtype_exact_video"><input type="radio" value="exact" name="searchtype" id="searchtype_exact_video" class="radioSelect">
                                Exact title
                            </label>
                            <label for="searchtype_keyword_video"><input type="radio" value="keyword" name="searchtype" id="searchtype_keyword_video" class="radioSelect">
                                Keyword search
                            </label>
                            <br>
                            <label class="hide" for="title_video">
                                title
                            </label>
                            <input type="search" value="" name="title" id="title_video">
                            <input type="submit" value="GO" class="btn btn-inverse">
                        </p>
                        <p style="padding:10px 0 0 5px;">
                            <a href="/eresources/index.php?sub=video">
                                <i class="fa fa-th-list"></i>
                                Browse Video Collections
                            </a>
                        </p>
      		        </form>
                </div>
            </div>
        </div>
        </div>
        <div class="d-none d-lg-block p-4 ask-us">
            <h4>ASK US</h4>
            <noscript>
                <p>Ask Us instant messaging requires JavaScript.</p></noscript><div class="askBubble">
                    <a class="askPopUp" href="//ca.libraryh3lp.com/chat/askus@chat.ca.libraryh3lp.com?title=Ask+Us&amp;theme=gota&amp;css=//lib.unb.ca/core/css-2015/libraryh3lp.unb.lib.css">Type here to CHAT.</a>
                <p>
                <a href="/help/ask.php"><span class="sr-only">Ask by:</span>
                    <span><i class="fa fa-envelope-o"></i> Email</span>
                    <span><i class="fa fa-mobile"></i> Text</span>
                    <span><i class="fa fa-phone"></i> Phone</span>
                    <span><i class="fa fa-user"></i> In-Person</span>
                </a>
            </p>
        </div>
    </div>
</div>
</div>';
    return [
      '#children' => $html,
    ];
  }

}
