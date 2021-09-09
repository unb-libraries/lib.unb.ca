<?php

namespace Drupal\records_management\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * The "Retention details" entity.
 *
 * @ContententityType(
 *   id = "retention_details",
 *   label = @Translation("Retention details"),
 *   label_plural = @Translation("Retention details"),
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *     }
 *   },
 *   base_table = "retention_details",
 *   admin_permission = "administer retention_details entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid"
 *   }
 * )
 */
class RetentionDetails extends ContentEntityBase implements RetentionDetailsInterface {

  /**
   * {@inheritDoc}
   */
  public function getTrigger() {
    return $this->get(self::FIELD_TRIGGER)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getDurationOffice() {
    return $this->get(self::FIELD_DURATION_OFFICE)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields[self::FIELD_TRIGGER] = BaseFieldDefinition::create('string')
      ->setLabel(t('Retention trigger'))
      ->setDescription(t('Defined event in which retention begins.'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setDisplayOptions('form', [
        'weight' => 0,
      ]);

    $fields[self::FIELD_DURATION_OFFICE] = BaseFieldDefinition::create('string')
      ->setLabel(t('Active'))
      ->setDescription(t('Time spent in office filling space.'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setDisplayOptions('form', [
        'weight' => 10,
      ]);

    return $fields;
  }

}
