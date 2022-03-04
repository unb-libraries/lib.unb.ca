<?php

namespace Drupal\eresources\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\lib_unb_custom_entity\Entity\ContentEntityInterface;
use Drupal\lib_unb_custom_entity\Entity\ContentEntityBase;

/**
 * Defines an eResources local metadata entity.
 *
 * @ContentEntityType(
 *   id = "eresources_local_metadata",
 *   label = @Translation("eResources Local Metadata"),
 *   label_plural = @Translation("eResources Local Metadata"),
 *   label_collection = @Translation("eResources Local Metadata"),
 *   handlers = {
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "form" = {
 *       "default" = "Drupal\lib_unb_custom_entity\Form\ContentEntityForm",
 *     },
 *     "access" = "Drupal\lib_unb_custom_entity\Entity\EntityAccessControlHandler",
 *   },
 *   base_table = "eresources_local_metadata",
 *   admin_permission = "administer eresources_local_metadata entities",
 *   entity_keys = {
 *     "id" = "id",
 *   },
 *   links = {
 *     "canonical" = "/admin/eresources/metadata/local/{eresources_local_metadata}",
 *     "add-form" = "/admin/eresources/metadata/local/add",
 *     "edit-form" = "/admin/eresources/metadata/local/{eresources_local_metadata}/edit",
 *   },
 *   field_ui_base_route = "entity.eresources_local_metadata.settings",
 * )
 */
class LocalMetadata extends ContentEntityBase implements ContentEntityInterface {

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

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

    $fields['help_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Help URL'))
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

    $fields['about_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('About URL'))
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

    $fields['zotero_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Zotero URL'))
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

    $fields['is_collection'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Is a Collection (Video tab only)'))
      ->setRequired(FALSE)
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }

}
