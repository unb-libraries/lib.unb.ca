<?php

namespace Drupal\ior_awards\Entity;

use Drupal\Core\Entity\ContentEntityBase;

/**
 * The "IOR Award" entity.
 *
 * @ContententityType(
 *   id = "ior_award",
 *   label = @Translation("Award"),
 *   label_plural = @Translation("Awards"),
 *   label_collection = @Translation("Awards"),
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_entity\Entity\Routing\HtmlRouteProvider"
 *     },
 *     "access" = "Drupal\custom_entity\Entity\Access\EntityAccessControlHandler"
 *   },
 *   base_table = "ior_award",
 *   admin_permission = "administer award entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/researchcommons/ior/contests/{contest}/submissions/{ior_submission}/awards/add",
 *     "delete-form" = "/researchcommons/ior/contests/{contest}/submissions/{ior_submission}/awards/{ior_award}/delete",
 *   },
 *   field_ui_base_route = "entity.ior_award.settings",
 * )
 */
class Award extends ContentEntityBase implements AwardInterface {

}
