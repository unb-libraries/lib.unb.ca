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
 *       "default" = "Drupal\records_management\Form\ScheduleForm",
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
  public function getOoprDetails() {
    return $this->get(self::FIELD_DETAILS_OOPR)
      ->entity;
  }

  /**
   * {@inheritDoc}
   */
  public function getOosrDetails() {
    return $this->get(self::FIELD_DETAILS_OOSR)
      ->entity;
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
  public function isVital() {
    return $this->get(self::FIELD_VITAL)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public function isPersonal() {
    return $this->get(self::FIELD_PERSONAL)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getApprovalDate() {
    return $this->get(self::FIELD_APPROVAL_DATE)
      ->date;
  }

  /**
   * {@inheritDoc}
   */
  public function getRevisionDate() {
    return $this->get(self::FIELD_REVISION_DATE)
      ->date;
  }

  /**
   * {@inheritDoc}
   */
  public function getNotes() {
    return $this->get(self::FIELD_NOTES)
      ->value;
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

    $fields[self::FIELD_OOPR] = BaseFieldDefinition::create('string')
      ->setLabel(t('Office of Primary Responsibility'))
      ->setCardinality(1)
      ->setDisplayOptions('form', [
        'weight' => 30,
      ]);

    $fields[self::FIELD_PURPOSE] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Purpose of Record'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSettings([
        'allowed_formats' => [
          'basic_html',
        ],
      ])
      ->setDisplayOptions('form', [
        'weight' => 40,
      ]);

    $fields[self::FIELD_SUMMARY] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description of Record (summary of content)'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSettings([
        'allowed_formats' => [
          'basic_html',
        ],
      ])
      ->setDisplayOptions('form', [
        'weight' => 50,
      ]);

    $fields[self::FIELD_DETAILS_OOPR] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Primary Office of Responsibility'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSetting('target_type', 'retention_details')
      ->setDisplayOptions('form', [
        'type' => 'inline_entity_form_simple',
        'weight' => 55,
      ]);

    $fields[self::FIELD_DETAILS_OOSR] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Secondary Office of Responsibility'))
      ->setRequired(FALSE)
      ->setCardinality(1)
      ->setSetting('target_type', 'retention_details')
      ->setDisplayOptions('form', [
        'type' => 'inline_entity_form_simple',
        'weight' => 56,
      ]);

    $fields[self::FIELD_RATIONALE] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Retention Rationale and Citation'))
      ->setRequired(FALSE)
      ->setCardinality(1)
      ->setSettings([
        'allowed_formats' => [
          'basic_html',
        ],
      ])
      ->setDisplayOptions('form', [
        'weight' => 60,
      ]);

    $fields[self::FIELD_VITAL] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Vital record'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSettings([
        'off_label' => t('No'),
        'on_label' => t('Yes'),
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_buttons',
        'weight' => 70,
      ]);

    $fields[self::FIELD_PERSONAL] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Personal information'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSettings([
        'off_label' => t('No'),
        'on_label' => t('Yes'),
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_buttons',
        'weight' => 80,
      ]);

    $fields[self::FIELD_APPROVAL_DATE] = BaseFieldDefinition::create('datetime_timezone')
      ->setLabel(t('Schedule creation date'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSettings([
        'timezone' => 'system',
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'weight' => 90,
      ]);

    $fields[self::FIELD_REVISION_DATE] = BaseFieldDefinition::create('datetime_timezone')
      ->setLabel(t('Revision date'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSettings([
        'timezone' => 'system',
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'weight' => 95,
      ]);

    $fields[self::FIELD_NOTES] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Notes / Additional Information'))
      ->setRequired(FALSE)
      ->setCardinality(1)
      ->setSettings([
        'allowed_formats' => [
          'basic_html',
        ],
      ])
      ->setDisplayOptions('form', [
        'weight' => 98,
      ]);

    return $fields;
  }

}
