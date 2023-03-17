<?php

namespace Drupal\ior\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginBase;

/**
 * Plugin implementation of the 'selection' entity_reference.
 *
 * @EntityReferenceSelection(
 *   id = "ior_contest",
 *   label = @Translation("Filter by an IOR contest."),
 *   group = "ior_contest",
 *   entity_types = {
 *     "ior_award",
 *   },
 *   weight = 0
 * )
 */
class ContestSelection extends SelectionPluginBase {

  /**
   * Build the query.
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   *   A query yielding all entities of the target type that are associated
   *   with the contest of the current submission.
   */
  protected function buildQuery() {
    $config = $this->getConfiguration();

    /** @var \Drupal\ior\Entity\SubmissionInterface $submission */
    $submission = $config['entity'];
    $target_type = $config['target_type'];

    return \Drupal::entityQuery($target_type)
      ->condition('field_contest', $submission->getContest()->id());
  }

  /**
   * {@inheritDoc}
   */
  public function getReferenceableEntities($match = NULL, $match_operator = 'CONTAINS', $limit = 0) {
    $target_type = $this->getConfiguration()['target_type'];
    $ids = $this->buildQuery()
      ->execute();

    $entities = \Drupal::entityTypeManager()->getStorage($target_type)->loadMultiple($ids);
    $options[$target_type] = array_map(function (EntityInterface $award) {
      return $award->label();
    }, $entities);

    return $options;
  }

  /**
   * {@inheritDoc}
   */
  public function countReferenceableEntities($match = NULL, $match_operator = 'CONTAINS') {
    return $this->buildQuery()
      ->count()
      ->execute();
  }

  /**
   * {@inheritDoc}
   */
  public function validateReferenceableEntities(array $ids) {
    return $this->buildQuery()
      ->execute();
  }

}
