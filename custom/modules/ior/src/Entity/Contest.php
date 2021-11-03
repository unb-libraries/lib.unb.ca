<?php

namespace Drupal\ior\Entity;

use Drupal\Core\Entity\ContentEntityBase;

/**
 * The "Contest" entity.
 *
 * @ContententityType(
 *   id = "contest",
 *   label = @Translation("Contest"),
 *   label_plural = @Translation("Contests"),
 *   label_collection = @Translation("Contests"),
 *   handlers = {
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "view_builder" = "Drupal\custom_entity\Entity\EntityTableViewBuilder",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_entity\Entity\Routing\HtmlRouteProvider"
 *     }
 *   },
 *   base_table = "ior_contest",
 *   revision_table = "ior_contest_revision",
 *   admin_permission = "administer contest entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "rid",
 *     "label" = "id",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/researchcommons/ior/{contest}",
 *     "add-form" = "/researchcommons/ior/add",
 *     "edit-form" = "/researchcommons/ior/{contest}/edit",
 *     "delete-form" = "/researchcommons/ior/{contest}/delete",
 *     "revisions" = "/researchcommons/ior/{contest}/revisions",
 *     "revision" = "/researchcommons/ior/{contest}/revisions/{contest_revision}",
 *     "revision-restore-form" = "/records/{contest}/revisions/{contest_revision}/restore",
 *   },
 *   field_ui_base_route = "entity.contest.settings",
 * )
 */
class Contest extends ContentEntityBase implements ContestInterface {

}
