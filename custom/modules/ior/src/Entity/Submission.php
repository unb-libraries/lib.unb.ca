<?php

namespace Drupal\ior\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

/**
 * The "Submission" entity.
 *
 * @ContententityType(
 *   id = "ior_submission",
 *   label = @Translation("Submission"),
 *   label_plural = @Translation("Submissions"),
 *   label_collection = @Translation("Submissions"),
 *   handlers = {
 *     "views_data" = "Drupal\ior\Entity\SubmissionViewsData",
 *     "form" = {
 *       "default" = "Drupal\ior\Form\SubmissionForm",
 *       "delete" = "Drupal\ior\Form\SubmissionDeleteForm"
 *     },
 *     "storage" = "Drupal\ior\Entity\Storage\SubmissionStorage",
 *     "route_provider" = {
 *       "html" = "Drupal\ior\Entity\Routing\SubmissionHtmlRouteProvider"
 *     },
 *     "access" = "Drupal\custom_entity\Entity\Access\EntityAccessControlHandler"
 *   },
 *   base_table = "ior_submission",
 *   revision_table = "ior_submission_revision",
 *   admin_permission = "administer submission entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "rid",
 *     "uuid" = "uuid",
 *     "published" = "published",
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

  use EntityPublishedTrait;

  /**
   * The contest.
   *
   * @var \Drupal\ior\Entity\ContestInterface
   *   A contest entity.
   */
  protected $contest;

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
  public function getContest() {
    return $this->contest;
  }

  /**
   * {@inheritDoc}
   */
  public function setContest(ContestInterface $contest) {
    $this->contest = $contest;
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $values, $entity_type, $bundle = FALSE, $translations = []) {
    parent::__construct($values, $entity_type, $bundle, $translations);
    if (array_key_exists('contest', $values)) {
      $this->contest = $values['contest'];
    }
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

    $fields += static::publishedBaseFieldDefinitions($entity_type);
    $fields[$entity_type->getKey('published')]
      ->setDefaultValue(FALSE);

    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    if (!$this->getContest()) {
      throw new MissingMandatoryParametersException('Submissions must be assigned to a contest upon creation.');
    }
    parent::preSave($storage);
  }

  /**
   * {@inheritDoc}
   */
  public function save() {
    $return = parent::save();
    $contest = $this->getContest();
    $contest->addSubmission($this);
    $contest->save();
    return $return;
  }

}
