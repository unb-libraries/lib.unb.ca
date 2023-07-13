<?php

namespace Drupal\eresources\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\lib_unb_custom_entity\Entity\ContentEntityBase;
use Drupal\lib_unb_custom_entity\Entity\ContentEntityInterface;

/**
 * Defines an eResources OCLC metadata entity.
 *
 * @ContentEntityType(
 *   id = "eresources_oclc_metadata",
 *   label = @Translation("eResources OCLC Metadata"),
 *   label_plural = @Translation("eResources OCLC Metadata"),
 *   label_collection = @Translation("eResources OCLC Metadata"),
 *   handlers = {
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\lib_unb_custom_entity\Entity\EntityAccessControlHandler",
 *   },
 *   base_table = "eresources_oclc_metadata",
 *   admin_permission = "administer eresources_oclc_metadata entities",
 *   entity_keys = {
 *     "id" = "id",
 *   },
 *   links = {
 *     "canonical" = "/admin/eresources/metadata/oclc/{eresources_oclc_metadata}"
 *   },
 *   field_ui_base_route = "entity.eresources_oclc_metadata.settings",
 * )
 */
class OclcMetadata extends ContentEntityBase implements ContentEntityInterface {

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setRequired(FALSE)
      ->setSettings([
        'max_length' => 4096,
      ]);

    return $fields;
  }

}
