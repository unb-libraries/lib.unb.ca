<?php

namespace Drupal\eresources\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\lib_unb_custom_entity\Entity\ContentEntityBase;
use Drupal\lib_unb_custom_entity\Entity\ContentEntityInterface;

/**
 * Defines an eResources record entity.
 *
 * @ContentEntityType(
 *   id = "eresources_record",
 *   label = @Translation("eResources Record"),
 *   label_plural = @Translation("eResources Records"),
 *   label_collection = @Translation("eResources Records"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\lib_unb_custom_entity\Entity\EntityAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\lib_unb_custom_entity\Form\ContentEntityForm",
 *       "delete" = "Drupal\lib_unb_custom_entity\Form\ContentEntityConfirmForm",
 *     },
 *   },
 *   base_table = "eresources_record",
 *   admin_permission = "administer eresources_record entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/eresources/record/{eresources_record}",
 *     "add-form" = "/admin/eresources/record/add",
 *     "edit-form" = "/admin/eresources/record/{eresources_record}/edit",
 *     "delete-form" = "/admin/eresources/record/{eresources_record}/delete",
 *     "collection" = "/admin/eresources/collection-redirect",
 *   },
 *   field_ui_base_route = "entity.eresources_record.settings",
 * )
 */
class Record extends ContentEntityBase implements ContentEntityInterface {

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['collection_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Collection ID'))
      ->setSetting('target_type', 'eresources_harvested_collection')
      ->setSetting('handler', 'default');

    $fields['local_metadata_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Local Metadata ID'))
      ->setSetting('target_type', 'eresources_local_metadata')
      ->setSetting('handler', 'default');

    $fields['oclc_metadata_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('OCLC Metadata ID'))
      ->setSetting('target_type', 'eresources_oclc_metadata')
      ->setSetting('handler', 'default');

    $fields['entry_uid'] = BaseFieldDefinition::create('string')
      ->setLabel(t('OCLC Entry UID'))
      ->setDescription(t('Used for Knowledge Base synchronization.'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 256,
        ]
      )
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['kb_data_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('KB Data Type'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 256,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

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

    $fields['author'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Author'))
      ->setRequired(FALSE)
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

    $fields['publisher'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Publisher'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 256,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['oclcnum'] = BaseFieldDefinition::create('string')
      ->setLabel(t('OCLC Number (OCN)'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 256,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['isbn'] = BaseFieldDefinition::create('string')
      ->setLabel(t('ISBN'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 256,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['issn'] = BaseFieldDefinition::create('string')
      ->setLabel(t('ISSN'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 256,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['eissn'] = BaseFieldDefinition::create('string')
      ->setLabel(t('eISSN'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 256,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('URL'))
      ->setRequired(FALSE)
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

    $fields['coverage'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Coverage'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 256,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['coverageenum'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Coverage enumeration'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 256,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['coverage_notes'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Coverage Notes'))
      ->setRequired(FALSE)
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

    $fields['collection_user_notes'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Collection User Notes'))
      ->setRequired(FALSE)
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

    $fields['location'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Location'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 256,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['linked_collections'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Linked Collections'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 1024,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 0,
        'settings' => [
          'rows' => 7,
          'placeholder' => "Title,URL\nTitle,URL",
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['is_local'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Mark as a custom local record'))
      ->setDescription(t('A boolean indicating whether the Record is a custom local record.'))
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 0,
      ]);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Mark as published'))
      ->setDescription(t('A boolean indicating whether the Record is published.'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 0,
      ]);

    return $fields;
  }

}
