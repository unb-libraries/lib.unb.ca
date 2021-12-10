<?php

namespace Drupal\ior_awards\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\ior\Entity\SubmissionInterface;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

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
 *       "default" = "Drupal\ior_awards\Form\AwardForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\ior_awards\Entity\Routing\AwardHtmlRouteProvider"
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

  /**
   * {@inheritDoc}
   */
  public function getSubmission() {
    return $this->get(self::FIELD_SUBMISSION)
      ->entity;
  }

  /**
   * {@inheritDoc}
   */
  public function setSubmission(SubmissionInterface $submission) {
    $this->set(self::FIELD_SUBMISSION, $submission);
  }

  /**
   * {@inheritDoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    if (!$this->getSubmission()) {
      throw new MissingMandatoryParametersException('Awards must be assigned to a submission upon creation.');
    }
    parent::preSave($storage);
  }

}
