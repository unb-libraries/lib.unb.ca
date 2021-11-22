<?php

namespace Drupal\ior\ParamConverter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\ParamConverter\ParamConverterInterface;
use Symfony\Component\Routing\Route;

/**
 * Param converter for parameters of type "entity:contest"ior_submission".
 */
class SubmissionParamConverter implements ParamConverterInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Get the entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   An entity type manager object.
   */
  protected function entityTypeManager() {
    return $this->entityTypeManager;
  }

  /**
   * Create a new param converter instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   An entity type manager object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function convert($value, $definition, $name, array $defaults) {
    $submission = $this->loadSubmission($value);
    $contest = $this->loadContest($submission->id());
    $submission->setContest($contest);
    return $submission;
  }

  /**
   * Load an ior_submission entity.
   *
   * @param int|string $key
   *   A key identifying an ior_submission entity.
   *
   * @return \Drupal\ior\Entity\SubmissionInterface
   *   An ior_submission entity.
   *
   * @noinspection PhpUnhandledExceptionInspection
   * @noinspection PhpIncompatibleReturnTypeInspection
   */
  protected function loadSubmission($key) {
    return $this
      ->entityTypeManager()
      ->getStorage('ior_submission')
      ->load($key);
  }

  /**
   * Load a contest entity matching the given submission ID.
   *
   * @param int $submission_id
   *   A ior_submission entity ID.
   *
   * @return \Drupal\ior\Entity\ContestInterface
   *   A contest entity.
   *
   * @noinspection PhpUnhandledExceptionInspection
   */
  protected function loadContest($submission_id) {
    return $this
      ->entityTypeManager()
      ->getStorage('contest')
      ->loadBySubmission($submission_id);
  }

  /**
   * {@inheritDoc}
   */
  public function applies($definition, $name, Route $route) {
    return isset($definition['type'])
      && $definition['type'] === 'entity:contest:ior_submission';
  }

}
