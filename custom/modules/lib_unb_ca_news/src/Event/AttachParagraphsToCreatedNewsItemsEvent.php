<?php

namespace Drupal\lib_unb_ca_news\Event;

use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigratePostRowSaveEvent;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Defines the migrate event subscriber.
 */
class AttachParagraphsToCreatedNewsItemsEvent implements EventSubscriberInterface {

  const BASE_URI = 'https://lib.unb.ca';
  const MIGRATION_ID = 'lib_unb_ca_news';
  const PATH_REWRITE_FILE = '/tmp/nginx_news_rewrites.txt;';

  /**
   * The current byline we are operating on.
   *
   * @var string[]
   */
  public $currentByline = [];

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
   * Attach content paragraphs to imported library_news nodes.
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
          $this->addContentNewsItem();
          $this->writeNode();
        }
      }
    }
  }

  /**
   * Create the body for a imported news item.
   */
  private function addContentNewsItem() {
    $body = $this->currentRow->getSourceProperty('body');
    if (empty(trim($body))) {
      $url = $this->currentRow->getSourceProperty('url');
      $body = "Content from $url failed.";
    }

    $this->currentParagraph = Paragraph::create([
      'type' => 'body_section',
      'field_body' => [
        'value' => $body,
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
    $this->setPostMetadata();
    $this->setPostAuthor();
    $this->currentNode->save();
  }

  /**
   * Determine the UID of the post author.
   *
   * @return int
   *   The user id of the desired post author.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function getPostAuthorUid() {
    $author = $this->getPostAuthor();

    // Preserve these authors, the rest go to monicac.
    $authors = [
      'Lesley Balcom' => 'lbalcom',
      'Sue Oliver' => 'suoliver',
    ];

    if (!empty($authors[$author])) {
      $author_id = $authors[$author];
    }
    else {
      $author_id = 'monicac';
    }

    // Cache the UID values so we are not loading user objects each item.
    $uid = \Drupal::state()->get("lib_news_cur_uid_{$author_id})");
    if (empty($uid)) {
      $users = \Drupal::entityTypeManager()->getStorage('user')
        ->loadByProperties(['name' => $author_id]);
      $user = reset($users);
      if ($user) {
        $uid = $user->id();
      }
      else {
        $uid = 1;
      }
      \Drupal::state()->set("lib_news_cur_uid_{$author_id})", $uid);
    }

    return $uid;
  }

  /**
   * Set the post metadata to the current node.
   */
  private function setPostMetadata() {
    $this->setBylineComponents();
    $this->setCreatedTime();
    $this->setPostAuthor();
    $this->setPostCategories();
    $this->writeOutNodeRedirect();
  }

  /**
   * Set the byline components from the current row.
   */
  private function setBylineComponents() {
    $this->currentByline = $this->getBylineComponents();
  }

  /**
   * Get the ByLine Components from the current row.
   */
  private function getBylineComponents() {
    $byline = $this->currentRow->getSourceProperty('byline');
    preg_match('/Posted on (.*), (.*) (.*), (.*) at (.*) by (.*) in (.*)/', $byline, $matches);
    return $matches;
  }

  /**
   * Set the news item's created time.
   */
  private function setCreatedTime() {
    $post_date = $this->getPostDate();
    if (!empty($post_date)) {
      $this->currentNode->setCreatedTime($post_date->getTimestamp());
    }
  }

  /**
   * Get the post date from the current row.
   */
  private function getPostDate() {
    $matches = $this->currentByline;
    $time_string = "$matches[2] $matches[3] $matches[4] $matches[5]";
    $time_string = str_ireplace([' AM', ' PM'], NULL, $time_string);
    return \DateTime::createFromFormat('F dS Y G:i', $time_string);
  }

  /**
   * Set the news item's created time.
   */
  private function setPostAuthor() {
    $this->currentNode->setOwnerId($this->getPostAuthorUid());
  }

  /**
   * Set the news item's post categories.
   */
  private function setPostCategories() {
    foreach ($this->getPostCategories() as $category) {
      $this->currentNode->field_categories[] = $category->id();
    }
  }

  /**
   * Get the post categories from the current row.
   */
  private function getPostCategories() {
    $categories = [];
    $matches = $this->currentByline;
    if (!empty($matches[7])) {
      preg_match_all("#<a.*?>(.*?)</a>#i", $matches[7], $link_matches);
      foreach ($link_matches[1] as $category) {
        $categories[] = $this->getCreateCategoryTerm($category);
      }
    }
    return $categories;
  }

  /**
   * Get a category term for a string, creating it if it does not exist.
   */
  private function getCreateCategoryTerm($name) {
    $category_id = NULL;
    if (!empty($name)) {
      $name_tid = $this->taxTermExists($name, 'name', 'categories');
      if (!empty($name_tid)) {
        $term = Term::load($name_tid);
      }
      else {
        $term = Term::create([
          'vid' => 'categories',
          'name' => $name,
        ]);
        $term->save();
      }
      return $term;
    }
    return NULL;
  }

  /**
   * Check if a taxonomy term exists.
   *
   * @param string $value
   *   The name of the term.
   * @param string $field
   *   The field to match when validating.
   * @param string $vocabulary
   *   The vid to match.
   *
   * @return mixed
   *   Contains an INT of the tid if exists, FALSE otherwise.
   */
  public function taxTermExists($value, $field, $vocabulary) {
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $vocabulary);
    $query->condition($field, $value);
    $tids = $query->execute();
    if (!empty($tids)) {
      foreach ($tids as $tid) {
        return $tid;
      }
    }
    return FALSE;
  }

  /**
   * Get the post author name from the current row.
   */
  private function getPostAuthor() {
    $matches = $this->currentByline;
    if (!empty($matches[6])) {
      return trim($matches[6]);
    }
    return NULL;
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

}
