<?php

namespace Drupal\portolan\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;

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

    // @todo Install "title" field [string(2048)]
    // @todo Install "author" field [multi-value, string(255)]
    // @todo Install "publication" field [string(1024)]
    // @todo Install "abstract" field [string(2048)]
    // @todo Install "note" field [string(1024)]
    // @todo Install "age_range" field [string(255)]
    // @todo Install "jurisdiction" field [multi-value, string(255)]
    // @todo Install "geographic" field [multi-value, string(255)]
    // @todo Install "descriptors" field [multi-value, string(255)]
    // @todo Install "call_num" field [string(255)]
    return $fields;
  }

}
