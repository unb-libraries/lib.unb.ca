<?php

namespace Drupal\eresources\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\lib_unb_custom_entity\Entity\ContentEntityInterface;
use Drupal\lib_unb_custom_entity\Entity\ContentEntityBase;

/**
 * Defines an eResources Harvested Collection entity.
 *
 * @ContentEntityType(
 *   id = "eresources_harvested_collection",
 *   label = @Translation("eResources Harvested Collection"),
 *   label_plural = @Translation("eResources Harvested Collections"),
 *   label_collection = @Translation("eResources Harvested Collections"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\lib_unb_custom_entity\Form\ContentEntityForm",
 *       "delete" = "Drupal\lib_unb_custom_entity\Form\ContentEntityConfirmForm",
 *     },
 *     "list_builder" = "Drupal\eresources\Entity\HarvestedCollectionListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\lib_unb_custom_entity\Entity\EntityAccessControlHandler",
 *     "storage" = "Drupal\eresources\Entity\Storage\HarvestedCollectionStorage",
 *   },
 *   base_table = "eresources_harvested_collection",
 *   admin_permission = "administer eresources_harvested_collection entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *   },
 *   links = {
 *     "canonical" = "/admin/eresources/harvested_collection/{eresources_harvested_collection}",
 *     "add-form" = "/admin/eresources/harvested_collections/add",
 *     "edit-form" = "/admin/eresources/harvested_collections/{eresources_harvested_collection}/edit",
 *     "delete-form" = "/admin/eresources/harvested_collections/{eresources_harvested_collection}/delete",
 *     "collection" = "/admin/eresources/harvested_collections",
 *     "synchronize" = "/admin/eresources/harvested_collections/{eresources_harvested_collection}/sync",
 *   }
 * )
 */
class HarvestedCollection extends ContentEntityBase implements ContentEntityInterface {

  /**
   * Tab options.
   *
   * @var array
   */
  public static $tabs = [
    'databases' => 'Article Databases',
    'reference' => 'Reference Materials',
  ];

  /**
   * Tab to KB data type LUT.
   *
   * @var array
   */
  private static $tabToKbDataType = [
    'databases' => 'DATA',
    'reference' => 'REF',
  ];

  /**
   * Gets the KB data type of the collection.
   */
  public function getKbDataType() {
    return self::$tabToKbDataType[$this->get('default_tab')->value];
  }

  /**
   * Gets the OCLC ID of the collection.
   */
  public function getOclcId() {
    return $this->get('oclc_id')->value;
  }

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['oclc_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('OCLC ID'))
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

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
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

    $fields['default_tab'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Default Tab'))
      ->setRequired(TRUE)
      ->setSettings([
        'allowed_values' => self::$tabs,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['last_sync'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Last Sync'))
      ->setRequired(FALSE)
      ->setSettings([
        'default_value' => '',
        'datetime_type' => 'date',
      ]);

    return $fields;
  }

}
