<?php

namespace Drupal\ior_awards\Entity;

use Drupal\Core\Entity\ContentEntityBase;

/**
 * The "Collection" entity.
 *
 * @ContententityType(
 *   id = "ior_collection",
 *   label = @Translation("Collection"),
 *   label_plural = @Translation("Collections"),
 *   label_collection = @Translation("Collections"),
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\ior\Form\SubmissionForm",
 *       "delete" = "Drupal\ior\Form\SubmissionDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_entity\Entity\Routing\HtmlRouteProvider"
 *     },
 *     "access" = "Drupal\custom_entity\Entity\Access\EntityAccessControlHandler"
 *   },
 *   base_table = "ior_collection",
 *   admin_permission = "administer collection entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/researchcommons/ior/contests/{contest}/collections/{ior_collection}",
 *     "add-form" = "/researchcommons/ior/contests/{contest}/collections/add",
 *     "edit-form" = "/researchcommons/ior/contests/{contest}/collections/{ior_collection}/edit",
 *     "delete-form" = "/researchcommons/ior/contests/{contest}/collections/{ior_collection}/delete",
 *   },
 *   field_ui_base_route = "entity.ior_collection.settings",
 * )
 */
class Collection extends ContentEntityBase implements CollectionInterface {

}
