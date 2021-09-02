<?php

namespace Drupal\records_management\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * The "Retention schedule" entity.
 *
 * @ContententityType(
 *   id = "schedule",
 *   label = @Translation("Retention schedule"),
 *   label_plural = @Translation("Retention schedules"),
 *   label_collection = @Translation("Retention schedules"),
 *   handlers = {
 *     "list_builder" = "Drupal\records_management\Entity\ScheduleListBuilder",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider"
 *     }
 *   },
 *   base_table = "schedule",
 *   revision_table = "schedule_revision",
 *   admin_permission = "administer schedule entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "rid",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/records/schedules/add",
 *     "edit-form" = "/records/schedules/{schedule}/edit",
 *     "delete-form" = "/records/schedules/{schedule}/delete",
 *     "collection" = "/records/schedules",
 *   }
 * )
 */
class Schedule extends ContentEntityBase implements ScheduleInterface {

  /**
   * {@inheritDoc}
   */
  public function getName() {
    return $this->get(self::FIELD_NAME)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getNumber() {
    return $this->get(self::FIELD_NUMBER)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getClassification() {
    return $this->get(self::FIELD_CLASSIFICATION)
      ->entity;
  }

  /**
   * {@inheritDoc}
   */
  public function getOfficeOfPrimaryResponsibility() {
    return $this->get(self::FIELD_OOPR)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getPurpose() {
    return $this->get(self::FIELD_PURPOSE)
      ->processed;
  }

  /**
   * {@inheritDoc}
   */
  public function getSummary() {
    return $this->get(self::FIELD_SUMMARY)
      ->processed;
  }

  /**
   * {@inheritDoc}
   */
  public function getRetentionRationale() {
    return $this->get(self::FIELD_RATIONALE)
      ->processed;
  }

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields[self::FIELD_NAME] = BaseFieldDefinition::create('string')
      ->setLabel(t('Record series name'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setDisplayOptions('form', [
        'weight' => 0,
      ]);

    $fields[self::FIELD_NUMBER] = BaseFieldDefinition::create('string')
      ->setLabel(t('Record schedule number'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->addPropertyConstraints('value', [
        'Regex' => [
          'pattern' => '/^[1-9]{1}[0-9]{3}(\.((0[1-9])|[1-9][0-9]))?$/',
        ],
      ])
      ->setDisplayOptions('form', [
        'weight' => 10
      ]);

    $fields[self::FIELD_CLASSIFICATION] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Classification'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSetting('target_type', 'classification')
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 20,
      ]);

    $fields[self::FIELD_OOPR] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Office of Primary Responsibility'))
      ->setCardinality(1)
      ->setSetting('allowed_values', [
        'FS' => t('Financial Services'),
      ])
      ->setDisplayOptions('form', [
        'weight' => 30,
      ]);

    $fields[self::FIELD_PURPOSE] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Purpose of Record'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setDisplayOptions('form', [
        'weight' => 40,
      ]);

    $fields[self::FIELD_SUMMARY] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description of Record (summary of content)'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setDisplayOptions('form', [
        'weight' => 50,
      ]);

    // @todo Install multi-value (max: 2) required "retention_details" entity_reference (retention_details) field.
    $fields[self::FIELD_RATIONALE] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Retention Rationale and Citation'))
      ->setRequired(FALSE)
      ->setCardinality(1)
      ->setDisplayOptions('form', [
        'weight' => 60,
      ]);

    // @todo Install required "vital" bool field.
    // @todo Install required "personal" bool field.
    // @todo Install required "approved" date field.
    // @todo Install required "last_revised" date field.
    return $fields;
  }

}
