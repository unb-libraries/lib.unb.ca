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

    $fields['alternate_title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Alternate Title'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 255,
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

    $fields['date_coverage'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Coverage/Publication Date'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 255,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['subscription_start_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Subscription Start Date'))
      ->setRequired(FALSE)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['subscription_end_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Subscription End Date'))
      ->setRequired(FALSE)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setRequired(FALSE)
      ->setSettings([
        'max_length' => 4096,
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'rows' => 6,
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['access_information'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Access Information'))
      ->setRequired(FALSE)
      ->setSettings([
        'max_length' => 4096,
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'rows' => 6,
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['license_status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('License Status'))
      ->setRequired(FALSE)
      ->setSettings([
        'allowed_values' => [
          'Y' => 'Licensed',
          'OA' => 'Free / Open Access',
          'T' => 'Trial',
          'C' => 'Cancelled',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['catalogue_location'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Catalog Location'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 255,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['call_number'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Call Number'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 255,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['is_collection'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Is a Collection'))
      ->setRequired(FALSE)
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
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
          'placeholder' => "Title,Collection UID\nTitle,Collection UID",
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['oclc_description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('OCLC Description'))
      ->setRequired(FALSE)
      ->setSettings([
        'max_length' => 4096,
      ]);

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
