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
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
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
    $department_ids = array_merge(... array_values(static::allowedDepartments()));
    $department_id = $this->get(self::FIELD_DEPARTMENT)
      ->value;
    return $department_ids[$department_id]
      ->render();
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
   * Allowed values callback for "department" field.
   *
   * @return array[]
   *   An array of VALUE => LABEL pairs.
   */
  public static function allowedDepartments() {
    return [
      t('Arts')->render() => [
        'FR-ART-ANT' => t('Anthropology'),
        'FR-ART-CAH' => t('Classics and Ancient History'),
        'FR-ART-CRW' => t('Creative Writing'),
        'FR-ART-CMS' => t('Culture and Media Studies'),
        'FR-ART-DRA' => t('Drama'),
        'FR-ART-ECO' => t('Economics'),
        'FR-ART-ENG' => t('English'),
        'FR-ART-ART' => t('Faculty of Arts (Fredericton)'),
        'SJ-ART-ART' => t('Faculty of Arts (Saint John)'),
        'FR-ART-FRE' => t('French'),
        'FR-ART-HIS' => t('History'),
        'SJ-ART-HIP' => t('History & Politics'),
        'SJ-ART-HUL' => t('Humanities & Languages'),
        'FR-ART-IDS' => t('International Development Studies'),
        'FR-ART-PHI' => t('Philosophy'),
        'FR-ART-PSC' => t('Political Science'),
        'FR-ART-PSY' => t('Psychology (Fredericton)'),
        'SJ-ART-PSY' => t('Psychology (Saint John)'),
        'SJ-ART-SSC' => t('Social Science'),
        'FR-ART-SOC' => t('Sociology'),
      ],
      t('Business')->render() => [
        'SJ-BUS-FAB' => t('Faculty of Business'),
        'FR-BUS-FAM' => t('Faculty of Management'),
      ],
      t('Computer Science')->render() => [
        'SJ-COM-COM' => t('Computer Science (Saint John)'),
        'FR-COM-COM' => t('Faculty of Computer Science (Fredericton)'),
      ],
      t('Education')->render() => [
        'FR-EDU-EDU' => t('Faculty of Education'),
      ],
      t('Engineering')->render() => [
        'FR-ENG-CHE' => t('Chemical Engineering'),
        'FR-ENG-CIV' => t('Civil Engineering'),
        'FR-ENG-ECO' => t('Electrical and Computer Engineering'),
        'SJ-ENG-ENG' => t('Engineering (Saint John)'),
        'FR-ENG-ENG' => t('Faculty of Engineering (Fredericton)'),
        'FR-ENG-GEO' => t('Geodesy and Geomatics Engineering'),
        'FR-ENG-MEC' => t('Mechanical Engineering'),
      ],
      t('Forestry')->render() => [
        'FR-FOR-FEM' => t('Faculty of Forestry & Environmental Management'),
      ],
      t('Kinesiology')->render() => [
        'FR-KIN-KIN' => t('Faculty of Kinesiology'),
      ],
      t('Law')->render() => [
        'FR-LAW-LAW' => t('Faculty of Law'),
      ],
      t('Leadership studies')->render() => [
        'FR-LEA-REN' => t('Renaissance College'),
      ],
      t('Nursing')->render() => [
        'FR-NUR-NUR' => t('Faculty of Nursing (Fredericton)'),
        'MC-NUR-NUR' => t('Faculty of Nursing (Moncton)'),
        'TO-NUR-HUM' => t('Humber College (Toronto)'),
        'SJ-NUR-NHS' => t('Nursing and Health Sciences (Saint John)'),
      ],
      t('Science')->render() => [
        'SJ-SCI-BIO' => t('Biological Sciences (Saint John)'),
        'FR-SCI-BIO' => t('Biology (Fredericton)'),
        'FR-SCI-CHE' => t('Chemistry'),
        'FR-SCI-EAS' => t('Earth Sciences'),
        'FR-SCI-SCI' => t('Faculty of Science (Fredericton)'),
        'SJ-SCI-SCI' => t('Faculty of Science, Applied Science & Engineering (Saint John)'),
        'FR-SCI-MAT' => t('Mathematics and Statistics (Fredericton)'),
        'SJ-SCI-MAT' => t('Mathematics and Statistics (Saint John)'),
        'FR-SCI-PHY' => t('Physics (Fredericton)'),
        'BI-SCI-PHY' => t('Physics (bi-campus)'),
      ],
    ];
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