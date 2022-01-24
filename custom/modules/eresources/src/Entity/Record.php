<?php

namespace Drupal\eresources\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\lib_unb_custom_entity\Entity\ContentEntityInterface;
use Drupal\lib_unb_custom_entity\Entity\ContentEntityBase;

/**
 * Defines an eResources record entity.
 *
 * @ContentEntityType(
 *   id = "eresources_record",
 *   label = @Translation("eResources Record"),
 *   label_plural = @Translation("eResources Records"),
 *   label_collection = @Translation("eResources Records"),
 *   handlers = {
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\lib_unb_custom_entity\Entity\EntityAccessControlHandler",
 *   },
 *   base_table = "eresources_record",
 *   admin_permission = "administer eresources_record entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/eresources/record/{eresources_record}"
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

    $fields['uid'] = BaseFieldDefinition::create('string')
      ->setLabel(t('OCLC UID'))
      ->setRequired(TRUE)
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

    $fields['oclcnum'] = BaseFieldDefinition::create('string')
      ->setLabel(t('OCLC Number (OCN)'))
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

    $fields['alt_title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Alternate Title'))
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

    $fields['url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('URL'))
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

    $fields['help_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Help URL'))
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

    $fields['about_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('About URL'))
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

    $fields['zotero_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Zotero URL'))
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

    $fields['subscribed_on'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Subscribed On'))
      ->setRequired(FALSE)
      ->setSettings([
        'default_value' => '',
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['subscription_expires'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Subscription Expires'))
      ->setRequired(FALSE)
      ->setSettings([
        'default_value' => '',
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime',
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

    $fields['description'] = BaseFieldDefinition::create('text')
      ->setLabel(t('Description'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 2048,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'text_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['date_coverage'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Date Coverage'))
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

    $fields['access_info'] = BaseFieldDefinition::create('text')
      ->setLabel(t('Access Information'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 2048,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'text_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['license_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('License Status'))
      ->setRequired(TRUE)
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

    $fields['kb_data_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('KB Data Type'))
      ->setRequired(TRUE)
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

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Record is published.'))
      ->setDefaultValue(TRUE);

    return $fields;
  }

}
