<?php

namespace Drupal\guides\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\custom_entity\Entity\EntityChangedTrait;
use Drupal\custom_entity\Entity\EntityCreatedTrait;
use Drupal\custom_entity\Entity\UserCreatedInterface;
use Drupal\custom_entity\Entity\UserEditedInterface;
use Drupal\Component\Utility\Html;
use PicoFeed\Reader\Reader;

/**
 * Defines a guide entity.
 *
 * @ContentEntityType(
 *   id = "guide",
 *   label = @Translation("Guide"),
 *   label_plural = @Translation("Guides"),
 *   label_collection = @Translation("Guides"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "route_provider" = {
 *       "html" = "Drupal\guides\Entity\Routing\GuideRouteProvider",
 *     },
 *     "access" = "Drupal\guides\Entity\Access\GuideAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\guides\Form\GuideForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *   },
 *   base_table = "guide",
 *   revision_table = "guide_revision",
 *   admin_permission = "administer guide entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "label" = "title",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/guides/{guide}",
 *     "add-form" = "/admin/guides/add",
 *     "edit-form" = "/admin/guides/{guide}/edit",
 *     "delete-form" = "/admin/guides/{guide}/delete",
 *     "revisions" = "/admin/guides/{guide}/revisions",
 *     "revision" = "/admin/guides/{guide}/revisions/{guide_revision}",
 *     "revision-restore-form" = "/admin/guides/{guide}/revisions/{guide_revision}/restore",
 *   },
 *   field_ui_base_route = "entity.guide.settings",
 * )
 */
class Guide extends ContentEntityBase implements ContentEntityInterface, UserEditedInterface, UserCreatedInterface {

  use EntityCreatedTrait;
  use EntityChangedTrait;

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings(
        [
          'max_length' => 1024,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['old_guide_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Old Guide ID'))
      ->setRequired(FALSE);

    $fields['is_subject_guide'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Is this a subject guide?'))
      ->setRequired(FALSE)
      ->setDefaultValue(TRUE);

    $fields['guide_categories'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Guide Categories'))
      ->setDescription(t('A guide is intended to have <b>ONLY ONE category.</b><br>You may add a second category for cross-listed courses or interdisciplinary guides. Otherwise, use Related Categories.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setCardinality(2)
      ->setSetting('target_type', 'guide_category')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('form', [
        'type' => 'entity_autocomplete',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['sections'] = BaseFieldDefinition::create('entity_reference_revisions')
      ->setLabel(t('Sections'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setCardinality(8)
      ->setSetting('handler_settings', ['target_bundles' => ['guide_section' => 'guide_section']])
      ->setSetting('target_type', 'paragraph')
      ->setSetting('handler', 'default:paragraph')
      ->setDisplayOptions('form', [
        'type' => 'paragraphs',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['related_guide_categories'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Related Guide Categories'))
      ->setDescription(t('Related guides categories.'))
      ->setRequired(FALSE)
      ->setRevisionable(TRUE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'guide_category')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('form', [
        'type' => 'entity_autocomplete',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['related_guides'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Related Guides'))
      ->setDescription(t('Select up to 5 related guides'))
      ->setRequired(FALSE)
      ->setRevisionable(TRUE)
      ->setCardinality(5)
      ->setSetting('target_type', 'guide')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('form', [
        'type' => 'entity_autocomplete',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['editors'] = BaseFieldDefinition::create('entity_reference_revisions')
      ->setLabel(t('Editors'))
      ->setRequired(FALSE)
      ->setRevisionable(TRUE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('handler_settings', ['target_bundles' => ['guide_editor' => 'guide_editor']])
      ->setSetting('target_type', 'paragraph')
      ->setSetting('handler', 'default:paragraph')
      ->setDisplayOptions('form', [
        'type' => 'paragraphs',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['feeds'] = BaseFieldDefinition::create('entity_reference_revisions')
      ->setLabel(t('Feeds'))
      ->setRequired(FALSE)
      ->setRevisionable(TRUE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('handler_settings', ['target_bundles' => ['guide_feed' => 'guide_feed']])
      ->setSetting('target_type', 'paragraph')
      ->setSetting('handler', 'default:paragraph')
      ->setDisplayOptions('form', [
        'type' => 'paragraphs',
        'weight' => 0,
        'group' => 'advanced',
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['unlisted'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Mark as unlisted'))
      ->setDescription(t('Hide this guide on category pages, search and guide listings (It can still be accessed directly)'))
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Mark as published'))
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 10,
      ]);

    $fields[self::FIELD_CREATED] = static::getCreatedBaseFieldDefinition($entity_type);
    $fields[self::FIELD_EDITED] = static::getEditedBaseFieldDefinition($entity_type);

    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    $linkStorage = $this->entityTypeManager()->getStorage('eresources_guide_link');

    // Remove existing entries.
    $query = $linkStorage->getQuery();
    $ids = $query->condition('guide', $this->id())->execute();
    if ($ids) {
      $links = $linkStorage->loadMultiple($ids);
      $linkStorage->delete($links);
    }

    foreach ($this->sections as $section) {
      $text = $section->entity->field_section_content->value;
      $document = Html::load($text);
      $xpath = new \DOMXPath($document);

      foreach ($xpath->query('//eresources') as $node) {
        $ids = explode(',', $node->nodeValue);
        foreach ($ids as $id) {
          $linkData = [
            'guide' => $this->id(),
            'eresource' => $id,
            'section' => $section->entity->id(),
          ];
          $link = $linkStorage->create($linkData);
          $link->save();
        }
      }
    }

    parent::postSave($storage, $update);
  }

  /**
   * {@inheritDoc}
   */
  public function label() {
    return $this->get('title')->value;
  }

  /**
   * Fetch the list of feed titles and items.
   */
  public function getFeeds() {
    $feeds = [];

    foreach ($this->feeds as $feedItem) {
      $feed = $feedItem->entity;
      $enabled = $feed->field_feed_enabled->getString();
      $url = $feed->field_feed_url->getString();

      if (!$enabled || empty($url)) {
        continue;
      }

      try {
        $reader = new Reader();
        $resource = $reader->download($url);

        $parser = $reader->getParser(
          $resource->getUrl(),
          $resource->getContent(),
          $resource->getEncoding()
        );

        $parsedFeed = $parser->execute();

        $title = $feed->field_feed_title->getString() ?: $parsedFeed->getTitle();
        $max = $feed->field_feed_items->getString();

        $count = 1;
        $items = [];
        foreach ($parsedFeed->items as $item) {
          $items[] = ['title' => $item->getTitle(), 'url' => $item->getUrl()];
          if ($count == $max) {
            break;
          }
          $count++;
        }
        $feeds[] = [
          'title' => $title,
          'items' => $items,
        ];
      }
      catch (Exception $e) {
        \Drupal::logger('guides')->error("Unable to parse feed: $url $e");
      }
    }

    return $feeds;
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheTags() {
    $tags = parent::getCacheTags();
    foreach ($this->get('editors') as $editorItem) {
      $editor = $editorItem->entity;
      if ($editor && $editor->field_display_editor->getString()) {
        $user = $editor->field_user->entity;
        $tags[] = 'user:' . $user->id();
      }
    }

    return $tags;
  }

}
