<?php

namespace Drupal\records_management\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;

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
 *     "label" = "id",
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
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // @todo Install required "name" string field.
    // @todo Install required "number" string field.
    // @todo Install required "classification" entity_reference (classification) field.
    // @todo Install required "oopr" string_list field. Allowed values to be defined.
    // @todo Install required "purpose" text field.
    // @todo Install required "description" text field.
    // @todo Install multi-value (max: 2) required "retention_details" entity_reference (retention_details) field.
    // @todo Install optional "rationale" text field.
    // @todo Install required "vital" bool field.
    // @todo Install required "personal" bool field.
    // @todo Install required "approved" date field.
    // @todo Install required "last_revised" date field.
    return $fields;
  }

}
