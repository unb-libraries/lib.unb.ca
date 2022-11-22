<?php

namespace Drupal\guides\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\custom_entity\Entity\EntityChangedTrait;
use Drupal\custom_entity\Entity\EntityCreatedTrait;
use Drupal\custom_entity\Entity\UserCreatedInterface;
use Drupal\custom_entity\Entity\UserEditedInterface;

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
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\guides\Entity\Access\GuideCategoryAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *   },
 *   base_table = "guide_category",
 *   admin_permission = "administer guide_category entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/guides/category/{guide_category}",
 *     "add-form" = "/admin/guides/category/add",
 *     "edit-form" = "/admin/guides/category/{guide_category}/edit",
 *     "delete-form" = "/admin/guides/category/{guide_category}/delete",
 *   },
 *   field_ui_base_route = "entity.guide_category.settings",
 * )
 */
class GuideCategory extends ContentEntityBase implements ContentEntityInterface, UserEditedInterface, UserCreatedInterface {

  use EntityChangedTrait;
  use EntityCreatedTrait;

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'default_value' => '',
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
      ->setSettings([
        'allowed_formats' => [
          'library_page_html',
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
      ->setSettings([
        'allowed_formats' => [
          'library_page_html',
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
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'guide_category')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('form', [
        'type' => 'entity_autocomplete',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['contacts'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Contacts'))
      ->setRequired(FALSE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default:user')
      ->setSetting('handler_settings', [
        'include_anonymous' => FALSE,
        'filter' => [
          'type' => 'role',
          'role' => ['guide_editor'],
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_autocomplete',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Mark as published'))
      ->setDescription(t('A boolean indicating whether the guide category is published.'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 10,
      ]);

    $fields[self::FIELD_CREATED] = static::getCreatedBaseFieldDefinition($entity_type);
    $fields[self::FIELD_EDITED] = static::getEditedBaseFieldDefinition($entity_type);

    return $fields;
  }

  /**
   * Get a list of guides that have marked this entity as their category.
   */
  public function getDetailedGuides() {
    $storage = $this->entityTypeManager()->getStorage('guide');
    $query = $storage->getQuery();
    $ids = $query->condition('guide_categories', $this->id())->execute();

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
    $ids = $query->condition('related_guide_categories', $this->id())->execute();

    if (!empty($ids)) {
      return $storage->loadMultiple($ids);
    }
    return [];
  }

}
