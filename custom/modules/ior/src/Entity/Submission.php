<?php

namespace Drupal\ior\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\custom_entity\Entity\EntityChangedTrait;
use Drupal\custom_entity\Entity\EntityCreatedTrait;
use Drupal\custom_entity_revisions\Entity\EntityRevisionsTrait;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

/**
 * The "Submission" entity.
 *
 * @ContententityType(
 *   id = "ior_submission",
 *   label = @Translation("Submission"),
 *   label_plural = @Translation("Submissions"),
 *   label_collection = @Translation("Submissions"),
 *   bundle_entity_type = "ior_submission_type",
 *   bundle_label = @Translation("Submission type"),
 *   handlers = {
 *     "views_data" = "Drupal\ior\Entity\SubmissionViewsData",
 *     "form" = {
 *       "default" = "Drupal\ior\Form\SubmissionForm",
 *       "edit" = "Drupal\ior\Form\SubmissionForm",
 *       "moderate" = "Drupal\ior\Form\SubmissionForm",
 *       "delete" = "Drupal\ior\Form\SubmissionDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\ior\Entity\Routing\SubmissionHtmlRouteProvider"
 *     },
 *     "storage" = "Drupal\ior\Entity\Storage\SubmissionStorage",
 *     "access" = "Drupal\ior\Entity\Access\SubmissionAccessControlHandler"
 *   },
 *   base_table = "ior_submission",
 *   revision_table = "ior_submission_revision",
 *   admin_permission = "administer submission entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "rid",
 *     "uuid" = "uuid",
 *     "bundle" = "type",
 *     "published" = "published",
 *   },
 *   links = {
 *     "canonical" = "/researchcommons/ior/contests/{contest}/submissions/{ior_submission}",
 *     "add-form" = "/researchcommons/ior/contests/{contest}/submissions/add",
 *     "edit-form" = "/researchcommons/ior/contests/{contest}/submissions/{ior_submission}/edit",
 *     "moderate-form" = "/researchcommons/ior/contests/{contest}/submissions/{ior_submission}/moderate",
 *     "delete-form" = "/researchcommons/ior/contests/{contest}/submissions/{ior_submission}/delete",
 *     "revisions" = "/researchcommons/ior/contests/{contest}/submissions/{ior_submission}/revisions",
 *     "revision" = "/researchcommons/ior/contests/{contest}/submissions/{ior_submission}/revisions/{ior_submission_revision}",
 *     "revision-restore-form" = "/researchcommons/ior/contests/{contest}/submissions/{ior_submission}/revisions/{ior_submission_revision}/restore",
 *   },
 *   field_ui_base_route = "entity.ior_submission_type.edit_form",
 * )
 */
class Submission extends ContentEntityBase implements SubmissionInterface {

  use EntityRevisionsTrait;
  use EntityPublishedTrait;
  use EntityCreatedTrait;
  use EntityChangedTrait;

  /**
   * {@inheritDoc}
   */
  public function label() {
    return $this->getTitle();
  }

  /**
   * {@inheritDoc}
   */
  public function getFirstName() {
    return $this->get(self::FIELD_FIRST_NAME)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getLastName() {
    return $this->get(self::FIELD_LAST_NAME)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getEmail() {
    return $this->get(static::FIELD_EMAIL)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getDepartment() {
    return $this->get(self::FIELD_DEPARTMENT)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getTitle() {
    return $this->get(static::FIELD_TITLE)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getDescription() {
    return $this->get(self::FIELD_DESCRIPTION)
      ->processed;
  }

  /**
   * {@inheritDoc}
   */
  public function getImageUrl() {
    $image_uri = $this
      ->get(self::FIELD_IMAGE)
      ->entity
      ->uri
      ->value;
    return file_create_url($image_uri);
  }

  /**
   * {@inheritDoc}
   */
  public function getContest() {
    return $this->get(self::FIELD_CONTEST)
      ->entity;
  }

  /**
   * {@inheritDoc}
   */
  public function setContest(ContestInterface $contest) {
    $this->set(self::FIELD_CONTEST, $contest);
  }

  /**
   * {@inheritDoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);
    $uri_route_parameters['contest'] = $this->getContest()->id();
    return $uri_route_parameters;
  }

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['contest'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Contest'))
      ->setRequired(TRUE)
      ->setRevisionable(FALSE)
      ->setSetting('target_type', 'contest')
      ->setDisplayConfigurable('view', FALSE)
      ->setDisplayConfigurable('form', FALSE);

    $fields['first_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('First name'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', ['weight' => 10])
      ->setDisplayOptions('form', ['weight' => 10]);

    $fields['last_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Last name'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', ['weight' => 20])
      ->setDisplayOptions('form', ['weight' => 20]);

    $fields['email'] = BaseFieldDefinition::create('email')
      ->setLabel(t('UNB Email'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', ['weight' => 30])
      ->setDisplayOptions('form', ['weight' => 30]);

    $fields['department'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Department & Faculty'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', ['weight' => 40])
      ->setDisplayOptions('form', ['weight' => 40]);

    $fields['website'] = BaseFieldDefinition::create('link')
      ->setLabel(t('Research URL'))
      ->setRequired(FALSE)
      ->setRevisionable(TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', ['weight' => 50])
      ->setDisplayOptions('form', ['weight' => 50]);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', ['weight' => 60])
      ->setDisplayOptions('form', ['weight' => 60]);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', ['weight' => 70])
      ->setDisplayOptions('form', [
        'third_party_settings' => [
          'allowed_formats' => [
            'hide_help' => TRUE,
            'hide_guidelines' => TRUE,
          ],
        ],
        'weight' => 70,
      ])
      ->setSettings([
        'allowed_formats' => [
          'no_media_html_ior_',
        ],
      ]);

    $fields['terms_conditions_accepted'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Terms & Conditions accepted'))
      ->setRequired(TRUE)
      ->setRevisionable(FALSE)
      ->setDisplayConfigurable('view', FALSE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', ['weight' => 80]);

    $fields += static::publishedBaseFieldDefinitions($entity_type);
    $fields[$entity_type->getKey('published')]
      ->setDefaultValue(FALSE);

    $fields[self::FIELD_CREATED] = static::getCreatedBaseFieldDefinition($entity_type);
    $fields[self::FIELD_EDITED] = static::getEditedBaseFieldDefinition($entity_type);

    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    if (!$this->getContest()) {
      throw new MissingMandatoryParametersException('Submissions must be assigned to a contest upon creation.');
    }

    if ($this->isNew()) {
      $this->set('moderation_state', 'draft');
    }

    parent::preSave($storage);
  }

}
