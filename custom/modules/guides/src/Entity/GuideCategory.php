<?php

namespace Drupal\guides\Entity;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\custom_entity\Entity\EntityChangedTrait;
use Drupal\custom_entity\Entity\EntityCreatedTrait;
use Drupal\custom_entity\Entity\UserCreatedInterface;
use Drupal\custom_entity\Entity\UserEditedInterface;
use Drupal\eresources\LocalResult;
use Drupal\guides\Entity\GuideCategoryInterface;
use Drupal\search_api\Entity\Index;

/**
 * Defines a guide_category entity.
 *
 * @ContentEntityType(
 *   id = "guide_category",
 *   label = @Translation("Guide Category"),
 *   label_plural = @Translation("Guide Categories"),
 *   label_collection = @Translation("Guide Categories"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "route_provider" = {
 *       "html" = "Drupal\guides\Entity\Routing\GuideCategoryRouteProvider",
 *     },
 *     "storage" = "Drupal\guides\Entity\Storage\GuideCategoryStorage",
 *     "access" = "Drupal\guides\Entity\Access\GuideCategoryAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\guides\Form\GuideCategoryForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *   },
 *   base_table = "guide_category",
 *   revision_table = "guide_category_revision",
 *   admin_permission = "administer guide_category entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *     "published" = "status",
 *     "revision" = "revision_id",
 *   },
 *   links = {
 *     "canonical" = "/guides/category/{guide_category}",
 *     "add-form" = "/admin/guides/category/add",
 *     "edit-form" = "/admin/guides/category/{guide_category}/edit",
 *     "delete-form" = "/admin/guides/category/{guide_category}/delete",
 *     "revisions" = "/admin/guides/category/{guide_category}/revisions",
 *     "revision" = "/admin/guides/category/{guide_category}/revisions/{guide_category_revision}",
 *     "revision-restore-form" = "/admin/guides/category/{guide_category}/revisions/{guide_category_revision}/restore",
 *   },
 *   field_ui_base_route = "entity.guide_category.settings",
 * )
 */
class GuideCategory extends ContentEntityBase implements GuideCategoryInterface, UserEditedInterface, UserCreatedInterface {

  use EntityChangedTrait;
  use EntityCreatedTrait;
  use EntityPublishedTrait;

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

    $fields['databases'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Top Databases'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'allowed_formats' => [
          'guide_category_databases',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'rows' => 6,
        'third_party_settings' => [
          'allowed_formats' => [
            'hide_help' => TRUE,
            'hide_guidelines' => TRUE,
          ],
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['references'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Top Reference Materials'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'allowed_formats' => [
          'guide_category_references',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'rows' => 6,
        'third_party_settings' => [
          'allowed_formats' => [
            'hide_help' => TRUE,
            'hide_guidelines' => TRUE,
          ],
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['related_guide_categories'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Related Guide Categories'))
      ->setRequired(FALSE)
      ->setRevisionable(TRUE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'guide_category')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
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

    $fields['other_contacts'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Add generic contacts to the More Information sidebar'))
      ->setRevisionable(TRUE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setRequired(FALSE)
      ->setSettings([
        'allowed_values' => [
          'askus' => 'Ask Us',
          'archives' => 'Archives & Special Collections',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_buttons',
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields += static::publishedBaseFieldDefinitions($entity_type);
    $fields[$entity_type->getKey('published')]
      ->setLabel(t('Mark as published'))
      ->setDescription(t('A boolean indicating whether the guide category is published.'))
      ->setDisplayOptions('form', [
        'weight' => 10,
      ]);

    $fields[self::FIELD_CREATED] = static::getCreatedBaseFieldDefinition($entity_type);
    $fields[self::FIELD_CREATOR] = static::getCreatorBaseFieldDefinition($entity_type);
    $fields[self::FIELD_EDITED] = static::getEditedBaseFieldDefinition($entity_type);
    $fields[self::FIELD_EDITOR] = static::getEditorBaseFieldDefinition($entity_type);

    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  public function save() {
    if (!$this->isNew()) {
      $this->setNewRevision(TRUE);
      $now = \Drupal::time()->getCurrentTime();
      $this->set(self::FIELD_EDITED, $now);
    }

    $result = parent::save();

    $aliasRepo = \Drupal::service('path_alias.repository');
    $thisPath = $this->toUrl()->toString();
    $thisAlias = $aliasRepo->lookupByAlias($thisPath, NULL);
    $aliasStorage = $this->entityTypeManager()->getStorage('path_alias');

    $types = ['databases', 'reference'];
    foreach ($types as $type) {
      $newAlias = "{$thisAlias['alias']}/resources/{$type}";
      $listPath = "{$thisAlias['path']}/resources/{$type}";

      $alias = NULL;
      $query = $aliasStorage->getQuery();
      $ids = $query->condition('path', $listPath)->execute();
      if (!empty($ids)) {
        $id = reset($ids);
        $alias = $aliasStorage->load($id);
        $alias->set('alias', $newAlias);
      }
      else {
        $alias = $aliasStorage->create([
          'path' => $listPath,
          'alias' => $newAlias,
          'status' => 1,
        ]);
      }
      $alias->save();
    }

    return $result;
  }

  /**
   * {@inheritDoc}
   */
  public function delete() {
    $aliasRepo = \Drupal::service('path_alias.repository');
    $thisPath = $this->toUrl()->toString();
    $aliasStorage = $this->entityTypeManager()->getStorage('path_alias');

    $types = ['databases', 'reference'];
    foreach ($types as $type) {
      $alias = "{$thisPath}/resources/{$type}";
      $aliasInfo = $aliasRepo->lookupByAlias($alias, NULL);

      $entity = $aliasStorage->load($aliasInfo['id']);
      $entity->delete();
    }

    return parent::delete();
  }

  /**
   * Get list of subject guides that have marked this entity as their category.
   */
  public function getDetailedGuides() {
    $storage = $this->entityTypeManager()->getStorage('guide');
    $query = $storage->getQuery();
    $ids = $query
      ->condition('status', 1)
      ->condition('unlisted', FALSE)
      ->condition('is_subject_guide', TRUE)
      ->condition('guide_categories', $this->id())
      ->sort('title', 'ASC')
      ->execute();

    if (!empty($ids)) {
      return $storage->loadMultiple($ids);
    }

    return [];
  }

  /**
   * Get a list of course guides that have marked this entity as their category.
   */
  public function getCourseGuides() {
    $storage = $this->entityTypeManager()->getStorage('guide');
    $query = $storage->getQuery();
    $ids = $query
      ->condition('status', 1)
      ->condition('unlisted', FALSE)
      ->condition('is_subject_guide', FALSE)
      ->condition('guide_categories', $this->id())
      ->sort('title', 'ASC')
      ->execute();

    if (!empty($ids)) {
      return $storage->loadMultiple($ids);
    }

    return [];
  }

  /**
   * Get a list of guides that have marked this entity as a related category.
   */
  public function getRelatedGuides() {
    $storage = $this->entityTypeManager()->getStorage('guide');
    $query = $storage->getQuery();
    $ids = $query
      ->condition('status', 1)
      ->condition('unlisted', FALSE)
      ->condition('related_guide_categories', $this->id())
      ->sort('title', 'ASC')
      ->execute();

    if (!empty($ids)) {
      return $storage->loadMultiple($ids);
    }
    return [];
  }

  /**
   * Get a sorted list of related guide categories.
   */
  public function getRelatedCategories() {
    $target_ids = array_column($this->related_guide_categories->getValue(), 'target_id');

    if (empty($target_ids)) {
      return [];
    }

    $storage = $this->entityTypeManager()->getStorage('guide_category');
    $query = $storage->getQuery();
    $ids = $query
      ->condition('status', 1)
      ->condition('id', $target_ids, 'IN')
      ->sort('title', 'ASC')
      ->execute();

    if (!empty($ids)) {
      return $storage->loadMultiple($ids);
    }
    return [];
  }

  /**
   * Get a count (in this category and total) of reference records.
   */
  public function getReferenceCount() {
    $result = [
      'current' => $this->countEresourcesIdsInHtml($this->get('references')->value),
      'total' => $this->getEresourcesByType('REF', TRUE),
    ];

    return $result;
  }

  /**
   * Get a count (in this category and total) of database records.
   */
  public function getDatabaseCount() {
    $result = [
      'current' => $this->countEresourcesIdsInHtml($this->get('databases')->value),
      'total' => $this->getEresourcesByType('DATA', TRUE),
    ];

    return $result;
  }

  /**
   * Count number of ids in eresources tag.
   */
  private function countEresourcesIdsInHtml($html) {
    $document = Html::load($html);
    $xpath = new \DOMXPath($document);

    $count = 0;
    foreach ($xpath->query('//eresources') as $node) {
      $ids = $node->getAttribute('ids');
      $count += count(explode(',', $ids));
    }

    return $count;
  }

  /**
   * Fetch the eresources in every guide in this category.
   */
  public function getEresourcesByType($type, $count = NULL) {
    $storage = $this->entityTypeManager()->getStorage('eresources_guide_link');
    $query = $storage->getQuery()
      ->condition('eresource.entity.status', 1)
      ->condition('eresource.entity.kb_data_type', "%{$type}%", 'LIKE')
      ->condition('guide.entity.status', 1)
      ->condition('guide.entity.guide_categories', $this->id());

    $linkIds = $query->execute();
    $links = $storage->loadMultiple($linkIds);
    $ids = array_map(function ($i) {
      return $i->get('eresource')->target_id;
    }, $links);

    $ids = array_unique($ids);
    if ($count) {
      return count($ids);
    }
    if (empty($ids)) {
      return [];
    }

    $index = Index::load('eresources');
    $indexQuery = $index->query(['limit' => 10000]);
    $indexQuery->addCondition('id', $ids, 'IN');
    $indexQuery->addCondition('status', TRUE);
    $indexQuery->sort('title', 'ASC');
    $results = $indexQuery->execute();

    if ($results->getResultCount() != 0) {
      $resources = array_map(function ($i) {
        return new LocalResult($i);
      }, $results->getResultItems());
      return $resources;
    }

    return [];
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheTagsToInvalidate() {
    $tags = parent::getCacheTagsToInvalidate();
    foreach ($this->get('editors') as $editorItem) {
      $editor = $editorItem->entity;
      if ($editor && $editor->field_display_editor->getString()) {
        if ($user = $editor->field_user->entity) {
          $tags[] = 'user:' . $user->id();
        }
      }
    }

    return $tags;
  }

}
