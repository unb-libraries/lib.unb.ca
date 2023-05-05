<?php

namespace Drupal\spaces\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the "Space type" entity.
 *
 * @ConfigEntityType(
 *   id = "space_type",
 *   label = @Translation("Space type"),
 *   label_singular = @Translation("Space type"),
 *   label_plural = @Translation("Space types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count space type",
 *     plural = "@count space types",
 *   ),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "form" = {
 *       "default" = "Drupal\custom_entity\Form\ConfigEntityBundleForm",
 *       "delete" = "Drupal\custom_entity\Form\ConfigEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_entity\Entity\Routing\HtmlRouteProvider",
 *     },
 *     "access" = "Drupal\custom_entity\Entity\Access\EntityAccessControlHandler",
 *   },
 *   config_prefix = "space_type",
 *   bundle_of = "space",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "bundle_class",
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/spaces/add",
 *     "edit-form" = "/admin/structure/spaces/manage/{space_type}",
 *     "delete-form" = "/admin/structure/spaces/manage/{space_type}/delete",
 *   },
 * )
 */
class SpaceType extends ConfigEntityBundleBase implements SpaceTypeInterface {

}
