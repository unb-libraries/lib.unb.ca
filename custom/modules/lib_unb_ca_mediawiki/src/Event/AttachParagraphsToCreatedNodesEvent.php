<?php

namespace Drupal\lib_unb_ca_mediawiki\Event;

use Drupal\media\Entity\Media;
use Drupal\migrate\Audit\AuditException;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigratePostRowSaveEvent;
use Drupal\node\Entity\Node;
use Drupal\node_path_taxonomy\Entity\NodeTaxonomyPath;
use Drupal\node_path_taxonomy\Entity\NodeTaxonomyPathRelationship;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\path_alias\Entity\PathAlias;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Defines the migrate event subscriber.
 */
class AttachParagraphsToCreatedNodesEvent implements EventSubscriberInterface {

  const BASE_URI = 'https://unbhistory.lib.unb.ca';
  const MIGRATION_ID = 'lib_unb_mediawiki';
  const PATH_REWRITE_FILE = '/tmp/nginx_rewrites.txt;';
  const PATH_TAXONOMY_VID = 'unb_libraries_page_paths';

  /**
   * The current node we are operating on.
   *
   * @var \Drupal\node\Entity\Node
   */
  public $currentNode = NULL;

  /**
   * The current paragraph to attach to the node.
   *
   * @var \Drupal\paragraphs\Entity\Paragraph
   */
  public $currentParagraph = NULL;

  /**
   * The current row.
   *
   * @var \Drupal\migrate\Row
   */
  public $currentRow = NULL;

  /**
   * The created Nodes for this row.
   *
   * @var int[]
   */
  public $destinationNids = [];

  /**
   * The current migration.
   *
   * @var \Drupal\migrate\Plugin\Migration
   */
  public $migration = NULL;

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [MigrateEvents::POST_ROW_SAVE => [['onPostRowSave', 0]]];
  }

  /**
   * Attach content paragraphs to imported library_page nodes.
   *
   * @param \Drupal\migrate\Event\MigratePostRowSaveEvent $event
   *   The event triggered.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function onPostRowSave(MigratePostRowSaveEvent $event) {
    $this->migration = $event->getMigration();

    if ($this->migration->id() == self::MIGRATION_ID) {
      $this->destinationNids = $event->getDestinationIdValues();

      foreach ($this->destinationNids as $destinationNid) {
        $this->currentRow = $event->getRow();
        $this->currentNode = Node::load($destinationNid);

        if (!empty($this->currentNode)) {
          $this->addNodePathRelationship();
          $this->createContentWithSidebar();
          $this->writeNode();
          $this->writeOutNodeRedirect();
        }
      }
    }
  }

  /**
   * Write out the nginx formatted old/new redirect for this URL.
   */
  private function writeOutNodeRedirect() {
    $old_url = trim($this->currentRow->getSourceProperty('url'));
    $old_path = str_replace(self::BASE_URI, '', $old_url);

    $aliasManager = \Drupal::service('path_alias.manager');
    $new_path = $aliasManager->getAliasByPath('/node/' . $this->currentNode->id());

    $padded_old_string = str_pad($old_path, 50, " ");
    $rewrite_string = "$padded_old_string$new_path;" . PHP_EOL;
    file_put_contents(self::PATH_REWRITE_FILE, $rewrite_string, FILE_APPEND | LOCK_EX);
  }

  /**
   * Add the node-path relationship for the current Node.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *
   * @see node_path_taxonomy_pathauto_alias_alter()
   */
  private function addNodePathRelationship() {
    $url = trim($this->currentRow->getSourceProperty('url'));
    // Add expected path for destination to source path.
    $url = str_replace(
      'https://unbhistory.lib.unb.ca',
      'https://unbhistory.lib.unb.ca/archives/unbhistory',
      $url
    );

    // Global URL replaces.
    $url = str_replace('gddm-new', 'gddm', $url);

    $file_parts = pathinfo($url);
    $uri_dir = $file_parts['dirname'];

    if ($uri_dir == self::BASE_URI) {
      $path = '/';
    }
    else {
      $path = str_replace(self::BASE_URI, '', $uri_dir);
    }

    // Add the relationship.
    NodeTaxonomyPathRelationship::addNodePathRelationshipFromPath($this->currentNode, self::PATH_TAXONOMY_VID, $path);
    $cur_path_term = NodeTaxonomyPath::getNodePathTerm($this->currentNode);

    // No path entry?
    if (empty($cur_path_term)) {

      throw new AuditException(
        $this->migration,
        t(
          'The path [@path] from [@url] does not have a corresponding path taxonomy term',
          [
            '@path' => $path,
            '@url' => $url,
          ]
        )
      );
    }

    // Add state value: used in node_path_taxonomy_pathauto_alias_alter().
    \Drupal::state()->set(NodeTaxonomyPath::getPathTaxonomyTidStateKey(), $cur_path_term->id());
  }

  /**
   * Determine if the imported row had sidebar content.
   *
   * @return bool
   *   TRUE if the imported content had sidebar content. FALSE otherwise.
   */
  private function pageHasSidebar() {
    return (
      !empty($this->currentRow->getSourceProperty('has_non_sidebar'))
      && !empty($this->currentRow->getSourceProperty('has_sidebar'))
    );
  }

  /**
   * Create content to attach to the imported node with a sidebar-based layout.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function createContentWithSidebar() {
    $main_paragraphs = [];
    $sidebar_paragraphs = [];
    if ($this->sidebarHasChatWidget()) {
      $sidebar_paragraphs[] = $this->getChatWidgetParagraph();
    }
    if ($this->sidebarHasTermHours()) {
      foreach ($this->getTermHoursBlockParagraphs() as $hours_paragraph) {
        $sidebar_paragraphs[] = $hours_paragraph;
      }
    }
    if ($this->sidebarHasUpcomingHours()) {
      $sidebar_paragraphs[] = $this->getUpcomingHoursBlockParagraph();
    }
    if ($this->pageHasSidebarLinkLists()) {
      foreach ($this->getSidebarLinkListParagraphs() as $sidebar_paragraph) {
        $sidebar_paragraphs[] = $sidebar_paragraph;
      }
    }

    $sidebar_paragraphs[] = $this->getSidebarMediawikiParagraph('ID_UNBHISTORY_NAV');
    $sidebar_paragraphs[] = $this->getSidebarMediawikiParagraph('ID_UNBHISTORY_HELP');
    $main_paragraphs[] = $this->getNonSidebarContentParagraph();

    $this->currentParagraph = Paragraph::create([
      'type' => 'body_sidebar_section',
      'field_column_1' => $main_paragraphs,
      'field_column_2' => $sidebar_paragraphs,
    ]);
  }

  /**
   * Determine if the imported row had a list of links in its sidebar.
   *
   * @return bool
   *   TRUE if the imported content had a list of links in its sidebar. FALSE otherwise.
   */
  private function pageHasSidebarLinkLists() {
    return !empty($this->currentRow->getSourceProperty('sidebar_link_lists'));
  }

  /**
   * Get the paragraph entity that contains the sidebar menu.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\paragraphs\Entity\Paragraph[]
   *   The paragraph containing the sidebar menu.
   */
  private function getSidebarLinkListParagraphs() {
    $paragraphs = [];

    for ($i = 0; $i < count($this->currentRow->getSourceProperty('sidebar_link_lists_titles')); $i++) {
      $title = $this->currentRow->getSourceProperty('sidebar_link_lists_titles')[$i];
      $body = $this->currentRow->getSourceProperty('sidebar_link_lists')[$i];

      $block_storage = \Drupal::entityTypeManager()->getStorage('block_content');
      $block = array_values($block_storage->loadByProperties([
        'type' => 'basic_block',
        'body' => $body,
      ]))[0];

      if (!isset($block)) {
        $block = $block_storage->create([
          'info' => $title,
          'body' => [
            'value' => $body,
            'format' => 'library_page_html',
          ],
          'type' => 'basic_block',
        ]);
        $block->save();
      }

      $block_plugin_id = 'block_content:' . $block->uuid();
      $paragraph = Paragraph::create([
        'type' => 'custom_block_section',
        'field_selected_block' => $block_plugin_id,
      ]);

      $block_config = $paragraph->get('field_selected_block')->first()->getValue();
      $block_config['settings']['label'] = $title;
      $paragraph->get('field_selected_block')->first()->setValue($block_config);

      $paragraph->save();
      $paragraphs[] = $paragraph;
    }

    return $paragraphs;
  }

  /**
   * Determine if the imported row sidebar had a chat widget.
   *
   * @return bool
   *   TRUE if the content had a chat widget in the sidebar. FALSE otherwise.
   */
  private function sidebarHasChatWidget() {
    return (
      !empty($this->currentRow->getSourceProperty('chatwidget_sidebar'))
      || !empty($this->currentRow->getSourceProperty('chatwidget_popup_sidebar'))
      || !empty($this->currentRow->getSourceProperty('chatwidget_offline_sidebar'))
    );
  }

  /**
   * Get the paragraph entity that contains the askus chat widget.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\paragraphs\Entity\Paragraph
   *   The paragraph containing the chat widget.
   */
  private function getChatWidgetParagraph() {
    if (!empty($this->currentRow->getSourceProperty('chatwidget_popup_sidebar'))) {
      $block_type = 'askus_popup';
    }
    else {
      $block_type = 'askus_embedded';
    }

    $paragraph = Paragraph::create([
      'type' => 'custom_block_section',
      'field_selected_block' => $block_type,
    ]);

    $block_config = $paragraph->get('field_selected_block')->first()->getValue();
    $block_config['settings']['label_display'] = 0;
    $paragraph->get('field_selected_block')->first()->setValue($block_config);

    $paragraph->save();
    return $paragraph;
  }

  /**pid
   * Determine if the imported row sidebar contained hours for the term.
   *
   * @return bool
   *   TRUE if the content had a chat widget in the sidebar. FALSE otherwise.
   */
  private function sidebarHasTermHours() {
    return !empty($this->currentRow->getSourceProperty('sidebar_hours_term'));
  }

  /**
   * Determine if the imported row sidebar contained hours for the next 7 days.
   *
   * @return bool
   *   TRUE if the content had a chat widget in the sidebar. FALSE otherwise.
   */
  private function sidebarHasUpcomingHours() {
    return !empty($this->currentRow->getSourceProperty('sidebar_hours_upcoming'));
  }

  /**
   * Get the paragraph entity that contains hours block(s).
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\paragraphs\Entity\Paragraph[]
   *   The paragraph containing the hours block(s).
   */
  private function getTermHoursBlockParagraphs() {
    $hours_blocks = $this->currentRow->getSourceProperty('sidebar_hours_term');
    if (!is_array($hours_blocks)) {
      $hours_blocks = [$hours_blocks];
    }

    $paragraphs = [];
    foreach ($hours_blocks as $hours_block) {
      $paragraph = Paragraph::create([
        'type' => 'custom_block_section',
        'field_selected_block' => 'term_hours_block',
      ]);

      $block_value = $paragraph->get('field_selected_block')->first()->getValue();
      $block_value['settings']['label'] = 'Hours';
      $block_value['settings']['body'] = $hours_block;

      $paragraph->get('field_selected_block')->first()->setValue($block_value);
      $paragraph->save();

      $paragraphs[] = $paragraph;
    }

    return $paragraphs;
  }

  /**
   * Get the paragraph entity that contains hours block(s).
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\paragraphs\Entity\Paragraph
   *   The paragraph containing the hours block.
   */
  private function getUpcomingHoursBlockParagraph() {
    $paragraph = Paragraph::create([
      'type' => 'custom_block_section',
      'field_selected_block' => 'upcoming_hours_block',
    ]);

    $block_config = $paragraph->get('field_selected_block')->first()->getValue();
    $block_config['settings']['label'] = 'Hours';
    $paragraph->get('field_selected_block')->first()->setValue($block_config);

    $paragraph->save();
    return $paragraph;
  }

  /**
   * Create the sidebar content for a mediawiki imported row.
   * 
   * @param string $id_const
   *  The migrate source constant cotaining the sidebar block's ID. 
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\paragraphs\Entity\Paragraph
   *   The paragraph containing the sidebar content.
   */
  private function getSidebarMediawikiParagraph($id_const) {
    $block_storage = \Drupal::entityTypeManager()->getStorage('block_content');
    $block_id = $this->currentRow->getSourceProperty('constants')[$id_const];
    $block = $block_storage->load($block_id);

    if ($block) {
      $uuid = $block->uuid();
      $plugin_id = "block_content:$uuid";
      $paragraph = Paragraph::create(['type' => 'custom_block_section']);
      $paragraph->field_selected_block->plugin_id = $plugin_id;

      $paragraph->field_selected_block->settings = [
        'id' => $plugin_id,
        'label' => 'Archives & Special Collections Sidebar',
        'label_display' => false,
        'provider' => 'block_content',
        'status' => true,
        'info' => '',
        'view_mode' => 'full',
      ];

      $paragraph->save();
      return $paragraph;
    }

    return;
  }

  /**
   * Create the main content for a sidebar-containing imported row.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\paragraphs\Entity\Paragraph
   *   The paragraph containing the main content.
   */
  private function getNonSidebarContentParagraph() {
    $non_sidebar = $this->currentRow->getSourceProperty('non_sidebar');    
    // Remove original copyright notice
    $pattern = '/© UNB Archives & Special Collections, \d{4}/s';
    $non_sidebar = preg_replace($pattern, '', $non_sidebar);
    // Remove all classes
    $match_class = '#(class\=")(.*?)(")#s';
    preg_replace($match_class, '', $non_sidebar);
    // Remove all comments
    $non_sidebar = $this->removeHtmlComments($non_sidebar);
    // Switch external http targets to https
    $non_sidebar = str_replace('http:', 'https:', $non_sidebar);
    // Migrate internal links
    $non_sidebar = $this->internalLinks($non_sidebar);
    // Swap images with corresponding previously migrated Drupal media 
    $non_sidebar = $this->swapImg($non_sidebar);
    // Replace <b> tags with <strong> for compatibility with format library_page_html
    $non_sidebar = str_replace('b>', 'strong>', $non_sidebar);
    // Add table classes
    $pattern = '/\<table.+?\>/s';
    $non_sidebar = preg_replace(
      $pattern, 
      '<table class="table table-bordered table-hover table-striped wikitable">',
      $non_sidebar
    );
    // Add TOC classes
    $non_sidebar = str_replace(
      '<div id="toc" class="toc',
      '<div id="toc" class="toc alert bg-light border mt-0',
      $non_sidebar
    );
    $non_sidebar = str_replace(
      '<h2 id="mw-toc-heading"',
      '<h2 id="mw-toc-heading" class="h4"',
      $non_sidebar
    );
    // Add caption classes
    $non_sidebar = str_replace('<caption>', '<caption class="h4">', $non_sidebar);
    // Remove all empty tags
    $non_sidebar = $this->removeEmptyTags($non_sidebar);

    $paragraph = Paragraph::create([
      'type' => 'body_section',
      'field_body' => [
        'value' => $non_sidebar,
        'format' => 'library_page_html',
      ],
    ]);
    
    $title = $this->currentRow->getSourceProperty('title');
    echo "\nSaving paragraph [$title]\n";
    $paragraph->save();
    return $paragraph;
  }

  /**
   * Migrate target of interal links.
   *
   * @param string $html
   * A string containing the HTML before processing.
   *
   * @return string
   * A string containing the HTML after processing.
   */
  private function internalLinks($html) {
    $match_href = '/href=["\'](.*?)["\']/is';
    preg_match_all($match_href, $html, $matches);
    
    foreach($matches[1] as $match) {
      // Only process if link is not a self-link (starts w/ #)
      if (!(strpos($match, '#') === 0)) {

        // Only process interal links
        if (!str_contains($match, 'https:')) {

          if (str_contains($match, 'File:')) {
            $replace = str_replace('File:', 'sites/default/files/unbhistory/', $match);
            $html = str_replace($match, $replace, $html);
          }
          elseif (strpos($match, '/index.php') === 0 or str_contains($match, 'unbhistory/index.php')) {
            $html = str_replace($match, '', $html);
          }
          else {
            $replace = "/archives/unbhistory$match";
            $html = str_replace($match, $replace, $html);
          }
        }
        else {
          // Handle internal-pointing "external" links
          $html = str_replace(
            'unbhistory.lib.unb.ca/index.php/',
            'unbhistory.lib.unb.ca/',
            $html
          );
          $html = str_replace(
            'unbhistory.lib.unb.ca/',
            'lib.unb.ca/archives/unbhistory/',
            $html
          );
        }
      }
    }

    // Recursively remove redundant link paths
    while (str_contains($html, 'archives/unbhistory/archives/unbhistory/')) {
      $html = str_replace(
        'archives/unbhistory/archives/unbhistory/',
        'archives/unbhistory/',
        $html
      );
    }
    
    return $html;
  }

  /**
   * Swap source <img> tags for <figure> with Drupal Media image location.
   *
   * @param string $html
   * A string containing the HTML before processing.
   *
   * @return string
   * A string containing the HTML after processing.
   */
  private function swapImg($html) {
    // Extract contents of elements div.thumbinner containing images
    $pattern = '#(<div class="thumb tright".*?</div>.*?</div>)#s';
    $search = preg_match_all($pattern, $html, $thumbs);
    $thumbs = array_unique($thumbs);
    
    foreach ($thumbs[0] as $thumb) {
      // Retrieve image filename from src attribute
      $pattern = '#<img[^>]+src="([^">]*\/([^">\/]+))"#s';
      $search = preg_match($pattern, $thumb, $filename);
      
      if (!empty($filename)) {
        $filename = $filename[2] ?? $filename;
        $filename = str_replace('px-', 'px_', $filename);
        $pattern = '#.*?px_#';
        $search = preg_match($pattern, $filename, $remove);
        $filename = str_replace($remove, '', $filename);
        // Retrieve media object UUID
        $media = $this->loadMediaByFilename($filename);

        if (!empty($media)) {
          // Retrieve caption
          $pattern = '#(<div class="thumbcaption">)(.*?)(</div>)#s';
          $search = preg_match($pattern, $thumb, $caption);
          $caption = $caption[2] ?? $caption;
          // Add caption to media object alt
          $media->field_media_image->alt = $caption;
          $media->save();
          // Retrieve media UUID
          $uuid = $media->uuid();
          // Build replacement <figure>
          $figure = "
            <figure class='mediawiki-figure caption caption-drupal-media image-style align-right'>
              <drupal-media data-align='right' data-entity-type='media' data-view-mode='colorbox_smr_linked_to_original' data-entity-uuid='$uuid'></drupal-media>
              <figcaption>$caption</figcaption>
            </figure>
          ";
          $html = str_replace($thumb, $figure, $html);
        }
      }
    }

    if (str_contains($html, 'thumbcaption')) {
      $title = $this->currentRow->getSourceProperty('title');
      echo "\nUnmatched image in page [$title]\n";
    }
    return $html;
  }
  
  /**
   * Load Drupal image media by filename.
   *
   * @param string $filename
   * A string containing name of the file.
   *
   * @return Drupal\media\Entity\Media
   * The loaded Media object.
   */
  private function loadMediaByFilename($filename) {
    // Load the file entity by filename.
    $files = \Drupal::entityTypeManager()
      ->getStorage('file')
      ->loadByProperties(['filename' => $filename]);
  
    if ($files) {
      $file = reset($files); // Get the first file entity.
      // Load the media entity by file ID.
      $media_entities = \Drupal::entityTypeManager()
        ->getStorage('media')
        ->loadByProperties(['name' => $filename]);
  
      if ($media_entities) {
        $media = reset($media_entities); // Get the first media entity.
        return $media;
      }
    }
    
    return NULL;
  }
    
  /**
   * Remove comments from HTML
   *
   * @param string $html
   * A string containing the HTML before processing.
   *
   * @return string
   * A string containing the HTML after processing.
   */
  private function removeHtmlComments($html) {
    return preg_replace('/\<\!\-\-(.|\s)*?\-\-\>/s', '', $html);
  }
  
  /**
   * Remove empty tags from HTML
   *
   * @param string $html
   * A string containing the HTML before processing.
   *
   * @return string
   * A string containing the HTML after processing.
   */
  private function removeEmptyTags($html) {
    // Remove paragraphs containing only breaks
    $pattern = '/\<p\>(\<br\>|\<br\/\>|\s)*\<\/p\>/s';
    $html = preg_replace($pattern, '', $html);
    // Regular expression to match empty HTML tags
    $pattern = '/\<(\w+)\b[^\>]*\>\s*\<\/\1\>/';
    // Remove empty tags
    $html = preg_replace($pattern, '', $html);
    // Check if there are nested empty tags
    while (preg_match($pattern, $html)) {
      $html = preg_replace($pattern, '', $html);
    }
    
    return $html;
  }

  /**
   * Create the main content for a imported row with no sidebar.
   */
  private function addContentNoSidebar() {
    $this->currentParagraph = Paragraph::create([
      'type' => 'body_section',
      'body' => [
        'value' => $this->currentRow->getSourceProperty('body'),
        'format' => 'library_page_html',
      ],
    ]);
  }

  /**
   * Write the imported node with the content updates applied.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function writeNode() {
    $this->currentParagraph->save();
    $this->currentNode->set('field_page_content', [$this->currentParagraph]);
    // Get original URL
    $og_url = $this->currentRow->getSourceProperty('url');
    // Update to lib URL
    $url = str_replace('https://unbhistory.lib.unb.ca', '/archives/unbhistory', $og_url);
    // Save the new alias unencoded
    $alias = PathAlias::create([
      'path' => '/node/' . $this->currentNode->id(),
      'alias' => rawurldecode($url),
    ]);
    $alias->save();
    $this->currentNode->save();
  }

  /**
   * Get a list of manually configured aliases.
   */
  private function getManualPathAliases() {
    return [
      self::BASE_URI . '/about/index.php' => '/about',
      self::BASE_URI . '/about/hours.php' => '/about/hours',
      self::BASE_URI . '/collections/index.php' => '/faculty/collections',
      self::BASE_URI . '/collections/clc/index.php' => '/collections/clc',
      self::BASE_URI . '/faculty/index.php' => '/faculty',
      self::BASE_URI . '/help/index.php' => '/help',
      self::BASE_URI . '/microforms/index.php' => '/microforms',
      self::BASE_URI . '/openaccess/index.php' => '/faculty/openaccess',
      self::BASE_URI . '/requests/docdel/extramural.php' => '/services/docdel/document-delivery-community-alumni-borrowers',
      self::BASE_URI . '/requests/docdel/policy-fees.php' => '/services/docdel/document-delivery-policies-fees',
      self::BASE_URI . '/services/index.php' => '/services',

      // This is for the blank 'front' page.
      'https://systems.lib.unb.ca/blank.html' => '/unb-libraries',
    ];
  }

}
