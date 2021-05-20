<?php

namespace Drupal\portolan\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the "Portolan record" entity.
 *
 * @ContentEntityType(
 *   id = "portolan_record",
 *   label = @Translation("Portolan record"),
 *   label_plural = @Translation("Portolan records"),
 *   label_collection = @Translation("Portolan records"),
 *   base_table = "portolan_record",
 *   admin_permission = "administer portolan_record entities",
 *   entity_keys = {
 *     "id" = "oclc_id",
 *     "label" = "title",
 *   },
 *   field_ui_base_route = "entity.portolan_record.settings",
 * )
 */
class PortolanRecord extends ContentEntityBase {

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSetting('max_length', 2048);

    $fields['author'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Author(s)'))
      ->setRequired(TRUE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED);

    $fields['publication'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Publication'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSetting('max_length', 1024);

    $fields['abstract'] = BaseFieldDefinition::create('text')
      ->setLabel(t('Abstract'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSetting('max_length', 2048);

    $fields['note'] = BaseFieldDefinition::create('text')
      ->setLabel(t('Note'))
      ->setRequired(FALSE)
      ->setCardinality(1)
      ->setSetting('max_length', 1024);

    // @todo Install "age_range" field [string(255)]
    // @todo Install "jurisdiction" field [multi-value, string(255)]
    // @todo Install "geographic" field [multi-value, string(255)]
    // @todo Install "descriptors" field [multi-value, string(255)]
    // @todo Install "call_num" field [string(255)]
    return $fields;
  }

}
