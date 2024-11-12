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
      'https://unbhistory.lib.unb.ca/unbhistory',
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

  /**
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
    $non_sidebar = $this->swapImg($non_sidebar);

    // Replace <b> tags with <strong> for compatibility with format library_page_html.
    str_replace('b>', 'strong>', $non_sidebar);
    // Remove all classes.
    $match_class = '#(class\=")(.*?)(")#';
    preg_replace($match_class, '', $non_sidebar);
    // Remove all comments.
    $match_comment = '#(<!--)(.*?)(-->)#';
    preg_replace($match_comment, '', $non_sidebar);
    // Remove all empty tags.
    $match_empty = '#<(\w+)(\s[^>]*)?>\s*<\/\1>#';
    preg_replace($match_empty, '', $non_sidebar);
    
    $paragraph = Paragraph::create([
      'type' => 'body_section',
      'field_body' => [
        'value' => $non_sidebar,
        'format' => 'library_page_html',
      ],
    ]);
    
    $paragraph->save();
    return $paragraph;
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
    $pattern = '#(<div class="thumbinner".*?</div></div>)#';
    $search = preg_match_all($pattern, $html, $thumbs);
    
    foreach ($thumbs as $thumb) {
      $thumb = $thumb[0] ?? '';
      // Retrieve image filename from src attribute
      $pattern = '#<img[^>]+src="([^">]*\/([^">\/]+))"#i';
      $search = preg_match($pattern, $thumb, $filename);
      $filename = $filename[2] ?? $filename;
      // Retrieve media object UUID
      $media = $filename ? $this->loadMediaByFilename($filename) : NULL;
      $uuid = $media ? $media->uuid() : NULL;
      // Retrieve caption
      $pattern = '#(<div class="thumbcaption">)(.*?)(</div>)#';
      $search = preg_match($pattern, $thumb, $caption);
      $caption = $caption[2] ?? $caption;
      // Retrieve image width
      $pattern = '#(width=")(.*?)(")#';
      $search = preg_match($pattern, $thumb, $width);
      $width = $width[2] ?? $width;
      // Retrieve image height
      $pattern = '#(height=")(.*?)(")#';
      $search = preg_match($pattern, $thumb, $height);
      $height = $height[2] ?? $height;
      // Build replacement <figure>
      $figure = "
        <figure style='width: $width; height: $height;'>
          <drupal-media data-align='right' data-entity-type='media' data-entity-uuid='$uuid'></drupal-media>
          <figcaption>$caption</figcaption>
        </figure>
      ";
      $html = str_replace($thumb, $figure, $html);
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
