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
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *     },
 *     "access" = "Drupal\custom_entity\Entity\Access\EntityAccessControlHandler",
 *   },
 *   base_table = "retention_details",
 *   revision_table = "retention_details_revision",
 *   admin_permission = "administer retention_details entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "rid",
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
  public function getDurationStorage() {
    return $this->get(self::FIELD_DURATION_STORAGE)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getDisposition() {
    return $this->get(self::FIELD_DISPOSITION)
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
      ->setRequired(FALSE)
      ->setRevisionable(TRUE)
      ->setCardinality(1)
      ->setDisplayOptions('view', [
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'weight' => 0,
      ]);

    $fields[self::FIELD_DURATION_OFFICE] = BaseFieldDefinition::create('string')
      ->setLabel(t('Active'))
      ->setDescription(t('Time spent in office filling space.'))
      ->setRequired(FALSE)
      ->setRevisionable(TRUE)
      ->setCardinality(1)
      ->setDisplayOptions('view', [
        'weight' => 10,
      ])
      ->setDisplayOptions('form', [
        'weight' => 10,
      ]);

    $fields[self::FIELD_DURATION_STORAGE] = BaseFieldDefinition::create('string')
      ->setLabel(t('Semi-active'))
      ->setDescription(t('Time spent in storage space.'))
      ->setRequired(FALSE)
      ->setRevisionable(TRUE)
      ->setCardinality(1)
      ->setDisplayOptions('view', [
        'weight' => 20,
      ])
      ->setDisplayOptions('form', [
        'weight' => 20,
      ]);

    $fields[self::FIELD_DISPOSITION] = BaseFieldDefinition::create('string')
      ->setLabel(t('Disposition'))
      ->setDescription(t('Method of disposal.'))
      ->setRequired(FALSE)
      ->setRevisionable(TRUE)
      ->setCardinality(1)
      ->setDisplayOptions('view', [
        'weight' => 30,
      ])
      ->setDisplayOptions('form', [
        'weight' => 30,
      ]);

    return $fields;
  }

}
