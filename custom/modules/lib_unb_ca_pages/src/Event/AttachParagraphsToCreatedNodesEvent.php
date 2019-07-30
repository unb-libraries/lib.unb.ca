<?php

namespace Drupal\lib_unb_ca_pages\Event;

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

  const BASE_URI = 'https://lib.unb.ca';
  const MIGRATION_ID = 'lib_unb_pages';
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

          if ($this->pageHasSidebar()) {
            $this->createContentWithSidebar();
          }
          else {
            $this->addContentNoSidebar();
          }

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

    $aliasManager = \Drupal::service('path.alias_manager');
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
    \Drupal::state()->set(NodeTaxonomyPath::getOverrideAliasStateKey(), $cur_path_term->id());

    // If the URL is in the manual override array, force it.
    $manual_aliases = $this->getManualPathAliases();
    if (array_key_exists($url, $manual_aliases)) {
      \Drupal::state()->set(NodeTaxonomyPath::getManualAliasStateKey(), $manual_aliases[$url]);
    }
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
    if ($this->sidebarHasHours()) {
      foreach ($this->getHoursBlockParagraphs() as $hours_paragraph) {
        $sidebar_paragraphs[] = $hours_paragraph;
      }
    }
    $sidebar_paragraphs[] = $this->getSidebarContentParagraph();
    $main_paragraphs[] = $this->getNonSidebarContentParagraph();
    $this->currentParagraph = Paragraph::create([
      'type' => 'body_sidebar_section',
      'field_column_1' => $main_paragraphs,
      'field_column_2' => $sidebar_paragraphs,
    ]);
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
    $paragraph = Paragraph::create([
      'type' => 'custom_block_section',
      'field_selected_block' => 'askus_embedded',
    ]);
    $paragraph->save();
    return $paragraph;
  }

  /**
   * Determine if the imported row sidebar had a chat widget.
   *
   * @return bool
   *   TRUE if the content had a chat widget in the sidebar. FALSE otherwise.
   */
  private function sidebarHasHours() {
    return !empty($this->currentRow->getSourceProperty('sidebar_hours'));
  }

  /**
   * Get the paragraph entity that contains hours block(s).
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\paragraphs\Entity\Paragraph[]
   *   The paragraph containing the hours block(s).
   */
  private function getHoursBlockParagraphs() {
    $hours_blocks = $this->currentRow->getSourceProperty('sidebar_hours');
    if (!is_array($hours_blocks)) {
      $hours_blocks = [$hours_blocks];
    }

    $paragraphs = [];
    foreach ($hours_blocks as $hours_block) {
      $paragraph = Paragraph::create([
        'type' => 'custom_block_section',
        'field_selected_block' => 'sidebar_hours_block',
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
   * Create the sidebar content for a sidebar-containing imported row.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\paragraphs\Entity\Paragraph
   *   The paragraph containing the sidebar content.
   */
  private function getSidebarContentParagraph() {
    $paragraph = Paragraph::create([
      'type' => 'body_section',
      'field_body' => [
        'value' => $this->currentRow->getSourceProperty('sidebar'),
        'format' => 'library_page_html',
      ],
    ]);
    $paragraph->save();
    return $paragraph;
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
    $paragraph = Paragraph::create([
      'type' => 'body_section',
      'field_body' => [
        'value' => $this->currentRow->getSourceProperty('non_sidebar'),
        'format' => 'library_page_html',
      ],
    ]);
    $paragraph->save();
    return $paragraph;
  }

  /**
   * Create the main content for a imported row with no sidebar.
   */
  private function addContentNoSidebar() {
    $this->currentParagraph = Paragraph::create([
      'type' => 'fullwidth_body_section',
      'field_body' => [
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
      self::BASE_URI . '/collections/index.php' => '/collections',
      self::BASE_URI . '/microforms/index.php' => '/microforms',
      self::BASE_URI . '/requests/docdel/extramural.php' => '/services/docdel/document-delivery-community-alumni-borrowers',
      self::BASE_URI . '/requests/docdel/policy-fees.php' => '/services/docdel/document-delivery-policies-fees',

      // This is for the blank 'front' page.
      'https://systems.lib.unb.ca/blank.html' => '/unb-libraries',
    ];
  }

}
