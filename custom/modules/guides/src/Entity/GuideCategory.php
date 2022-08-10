<?php

namespace Drupal\guides\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\custom_entity\Entity\EntityChangedTrait;
use Drupal\custom_entity\Entity\EntityCreatedTrait;

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
 *     "access" = "Drupal\custom_entity\Entity\Access\EntityAccessControlHandler",
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
 *     "canonical" = "/guide/category/{guide)_category}",
 *     "add-form" = "/admin/guide/category/add",
 *     "edit-form" = "/admin/guide/category/{guide_category}/edit",
 *     "delete-form" = "/admin/guide/category/{guide_category}/delete",
 *   },
 *   field_ui_base_route = "entity.guide_category.settings",
 * )
 */
class GuideCategory extends ContentEntityBase implements ContentEntityInterface {

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
          'no_media_html',
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
          'no_media_html',
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
      ->setSetting('handler', 'default:user')
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
      ->setSetting('handler', 'default')
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

    return $fields;
  }

}
