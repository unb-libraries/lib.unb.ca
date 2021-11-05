<?php

namespace Drupal\ior\Entity;

use Drupal\Core\Entity\ContentEntityBase;

/**
 * The "Submission" entity.
 *
 * @ContententityType(
 *   id = "ior_submission",
 *   label = @Translation("Submission"),
 *   label_plural = @Translation("Submissions"),
 *   label_collection = @Translation("Submissions"),
 *   handlers = {
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_entity\Entity\Routing\HtmlRouteProvider"
 *     }
 *   },
 *   base_table = "ior_submission",
 *   revision_table = "ior_submission_revision",
 *   admin_permission = "administer submission entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "rid",
 *     "label" = "id",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/researchcommons/ior/{contest}/submissions/{ior_submission}",
 *     "add-form" = "/researchcommons/ior/{contest}/submissions/add",
 *     "edit-form" = "/researchcommons/ior/{contest}/submissions/{ior_submission}/edit",
 *     "delete-form" = "/researchcommons/ior/{contest}/submissions/{ior_submission}/delete",
 *     "revisions" = "/researchcommons/ior/{contest}/submissions/{ior_submission}/revisions",
 *     "revision" = "/researchcommons/ior/{contest}/submissions/{ior_submission}/revisions/{ior_submission_revision}",
 *     "revision-restore-form" = "/researchcommons/ior/{contest}/submissions/{ior_submission}/revisions/{ior_submission_revision}/restore",
 *   },
 *   field_ui_base_route = "entity.ior_submission.settings",
 * )
 */
class Submission extends ContentEntityBase implements SubmissionInterface {

}
