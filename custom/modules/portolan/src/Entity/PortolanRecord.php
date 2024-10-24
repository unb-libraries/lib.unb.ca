<?php

namespace Drupal\portolan\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\portolan\Field\TrimmedTitle;

/**
 * Defines the "Portolan record" entity.
 *
 * @ContentEntityType(
 *   id = "portolan_record",
 *   label = @Translation("Portolan record"),
 *   label_plural = @Translation("Portolan records"),
 *   label_collection = @Translation("Portolan records"),
 *   handlers = {
 *     "views_data" = "Drupal\portolan\Entity\PortolanRecordViewsData",
 *     "access" = "Drupal\portolan\Entity\Access\PortolanRecordAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider"
 *     }
 *   },
 *   base_table = "portolan_record",
 *   admin_permission = "administer portolan_record entities",
 *   entity_keys = {
 *     "id" = "oclc_id",
 *     "label" = "title",
 *   },
 *   links = {
 *     "canonical" = "/clc/portolan/records/{portolan_record}"
 *   },
 *   field_ui_base_route = "entity.portolan_record.settings",
 * )
 */
class PortolanRecord extends ContentEntityBase implements PortolanRecordInterface {

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields[self::FIELD_OCLC_ID] = BaseFieldDefinition::create('string')
      ->setLabel(t('OCLC ID'))
      ->setRequired(TRUE)
      ->setCardinality(1);

    $fields[self::FIELD_TITLE] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSetting('max_length', 2048);

    $fields[self::FIELD_TITLE_TRIMMED] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title (trimmed)'))
      ->setDescription(t('Title which contains no leading stop words.'))
      ->setCardinality(1)
      ->setComputed(TRUE)
      ->setClass(TrimmedTitle::class)
      ->setSetting('max_length', 2048);

    $fields[self::FIELD_AUTHOR] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Author(s)'))
      ->setRequired(TRUE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSettings([
        'target_type' => 'taxonomy_term',
        'handler_settings' => [
          'target_bundles' => [
            self::AUTHOR_VID => self::AUTHOR_VID,
          ],
        ],
      ]);

    $fields[self::FIELD_PUBLICATION] = BaseFieldDefinition::create('string')
      ->setLabel(t('Publication'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSetting('max_length', 1024);

    $fields[self::FIELD_ABSTRACT] = BaseFieldDefinition::create('text')
      ->setLabel(t('Abstract'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSetting('max_length', 4096);

    $fields[self::FIELD_NOTE] = BaseFieldDefinition::create('text')
      ->setLabel(t('Note'))
      ->setRequired(FALSE)
      ->setCardinality(1)
      ->setSetting('max_length', 1024);

    $fields[self::FIELD_AGE_RANGE] = BaseFieldDefinition::create('string')
      ->setLabel(t('Age range'))
      ->setRequired(TRUE)
      ->setCardinality(1);

    $fields[self::FIELD_JURISDICTION] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Jurisdiction'))
      ->setRequired(TRUE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSettings([
        'target_type' => 'taxonomy_term',
        'handler_settings' => [
          'target_bundles' => [
            self::JURISDICTION_VID => self::JURISDICTION_VID,
          ],
        ],
      ]);

    $fields[self::FIELD_LOCATION] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Geographic location'))
      ->setRequired(TRUE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSettings([
        'target_type' => 'taxonomy_term',
        'handler_settings' => [
          'target_bundles' => [
            self::LOCATION_VID => self::LOCATION_VID,
          ],
        ],
      ]);

    $fields[self::FIELD_DESCRIPTOR] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Descriptor(s)'))
      ->setRequired(TRUE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSettings([
        'target_type' => 'taxonomy_term',
        'handler_settings' => [
          'target_bundles' => [
            self::DESCRIPTOR_VID => self::DESCRIPTOR_VID,
          ],
        ],
      ]);

    $fields[self::FIELD_CALL_NUMBER] = BaseFieldDefinition::create('string')
      ->setLabel(t('Call number'))
      ->setRequired(TRUE)
      ->setCardinality(1);

    return $fields;
  }

}
