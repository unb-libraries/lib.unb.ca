<?php

namespace Drupal\spaces\Entity;

use Drupal\Core\Entity\ContentEntityBase;

/**
 * Defines the "Space" entity.
 *
 * @ContentEntityType(
 *   id = "space",
 *   label = @Translation("Space"),
 *   label_singular = @Translation("Space"),
 *   label_plural = @Translation("Spaces"),
 *   label_count = @PluralTranslation(
 *     singular = "@count space",
 *     plural = "@count spaces",
 *   ),
 *   bundle_label = @Translation("Space type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_entity\Entity\Routing\HtmlRouteProvider",
 *     },
 *     "access" = "Drupal\custom_entity\Entity\Access\EntityAccessControlHandler",
 *     "permissions" = "Drupal\custom_entity\Entity\Access\EntityPermissionsHandler",
 *   },
 *   admin_permission = "administer space entities",
 *   base_table = "space",
 *   translatable = FALSE,
 *   bundle_entity_type = "space_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "bundle" = "type",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/explore-spaces/{space}",
 *     "add-form" = "/explore-spaces/add/{space_type}",
 *     "edit-form" = "/explore-spaces/{space}/edit",
 *     "delete-form" = "/explore-spaces/{space}/delete",
 *   },
 *   field_ui_base_route = "entity.space_type.edit_form",
 * )
 */
class Space extends ContentEntityBase implements SpaceInterface {

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->get('field_name')
      ->value;
  }

}
