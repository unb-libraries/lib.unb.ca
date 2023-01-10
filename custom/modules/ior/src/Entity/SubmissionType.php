<?php

namespace Drupal\ior\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Submission type entity.
 *
 * @ConfigEntityType(
 *   id = "ior_submission_type",
 *   label = @Translation("Submission type"),
 *   label_plural = @Translation("Submission types"),
 *   label_collection = @Translation("Submission types"),
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\ior\Form\SubmissionTypeForm",
 *       "delete" = "Drupal\ior\Form\SubmissionTypeDeleteForm",
 *     },
 *     "list_builder" = "Drupal\ior\Entity\SubmissionTypeListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\custom_entity\Entity\Routing\HtmlRouteProvider",
 *     },
 *     "access" = "Drupal\custom_entity\Entity\Access\EntityAccessControlHandler",
 *   },
 *   config_prefix = "ior_submission_type",
 *   bundle_of = "ior_submission",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *   },
 *   links = {
 *     "add-form" = "/researchcommons/ior/submission-types/add",
 *     "edit-form" = "/researchcommons/ior/submission-types/{ior_submission_type}/edit",
 *     "delete-form" = "/researchcommons/ior/submission-types/{ior_submission_type}/delete",
 *     "collection" = "/researchcommons/ior/submission-types",
 *   }
 * )
 */
class SubmissionType extends ConfigEntityBundleBase {

}
