<?php

namespace Drupal\ior\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

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
 *       "default" = "Drupal\ior\Form\SubmissionForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\ior\Entity\Routing\SubmissionHtmlRouteProvider"
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

  /**
   * The contest.
   *
   * @var \Drupal\ior\Entity\ContestInterface
   *   A contest entity.
   */
  protected $contest;

  /**
   * Get the contest.
   *
   * @return \Drupal\ior\Entity\ContestInterface
   *   A contest entity.
   */
  public function getContest() {
    if (!$this->contest && !$this->isNew()) {
      /* @noinspection PhpUnhandledExceptionInspection */
      $query = $this->entityTypeManager()
        ->getStorage('contest')
        ->getQuery()
        ->condition('field_submissions', $this->id(), 'CONTAINS');

      if (!empty($contests = $query->execute())) {
        $this->contest = $contests[array_keys($contests)[0]];
      }
    }
    return $this->contest;
  }

  /**
   * {@inheritDoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);
    $uri_route_parameters['contest'] = $this->getContest();
    return $uri_route_parameters;
  }

  /**
   * Allowed values callback for "department" field.
   *
   * @param \Drupal\Core\Field\FieldStorageDefinitionInterface $definition
   *   The field definition.
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   The entity.
   * @param bool $cacheable
   *   Whether the result should be cacheable.
   *
   * @return array[]
   *   An array of VALUE => LABEL pairs.
   */
  public static function allowedDepartments(FieldStorageDefinitionInterface $definition, FieldableEntityInterface $entity, bool $cacheable) {
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

}
