<?php

namespace Drupal\ior\Plugin\views\filter;

use Drupal\content_moderation\Plugin\views\filter\ModerationStateFilter;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a filter for the permission and moderation state of an entity.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("moderation_state_permission_filter")
 */
class ModerationStatePermissionFilter extends ModerationStateFilter {

  /**
   * The currently logged-in user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Get the currently logged-in user.
   *
   * @return \Drupal\Core\Session\AccountProxyInterface
   *   A user account.
   */
  protected function currentUser() {
    return $this->currentUser;
  }

  /**
   * Creates a new moderation state permission filter instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   An entity type manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $bundle_info
   *   An entity type bundle info object.
   * @param \Drupal\Core\Entity\EntityStorageInterface $workflow_storage
   *   A storage handler for workflow entities.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The currently logged-in user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, EntityTypeBundleInfoInterface $bundle_info, EntityStorageInterface $workflow_storage, AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $bundle_info, $workflow_storage);
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    /** @var \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager */
    $entity_type_manager = $container->get('entity_type.manager');
    /** @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info */
    $entity_type_bundle_info = $container->get('entity_type.bundle.info');
    /** @var \Drupal\Core\Session\AccountProxyInterface $current_user */
    $current_user = $container->get('current_user');

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $entity_type_manager,
      $entity_type_bundle_info,
      $entity_type_manager->getStorage('workflow'),
      $current_user
    );
  }

  /**
   * {@inheritDoc}
   *
   * Refine the query to filter on those moderation states returned by
   * getPermittedStates(). If that method returns an empty result, the query
   * must not include any records.
   *
   * @throws \Exception
   */
  public function query() {
    parent::query();

    foreach ($permitted_states = $this->getPermittedStates() as $workflow => $states) {
      $this->query->addWhere('moderation_state_permissions', "$this->tableAlias.workflow", $workflow, '=');
      $this->query->addWhere('moderation_state_permissions', "$this->tableAlias.$this->realField", $states, 'IN');
    }

    if (empty($permitted_states)) {
      $this->query->addWhere('moderation_state_permissions', "$this->tableAlias.$this->realField", NULL, '=');
    }
  }

  /**
   * Get the permission based states by which should be filtered.
   *
   * @return array
   *   An array of array containing state IDs (without workflow prefix)
   *   grouped by workflow ID.
   *   Example: Given an entity of type "ior_submission" has a workflow
   *   "submission_review" which contains states "submitted", "rejected". The
   *   currently logged-in user has permission to "view submitted
   *   ior_submission entities". The resulting array would look as follows:
   *   [
   *     ior_submission_review => [
   *       submitted
   *     ]
   *   ]
   *
   * @throws \Exception
   */
  protected function getPermittedStates() {
    $permitted_states = [];
    foreach ($this->getValueOptions() as $states) {
      foreach ($states as $state => $state_label) {
        list($workflow_id, $state_id) = explode('-', $state);
        $permission = "view {$state_id} {$this->getEntityType()} entities";
        if ($this->currentUser()->hasPermission($permission)) {
          $permitted_states[$workflow_id][] = $state_id;
        }
      }
    }
    $this->calculateDependencies();
    return array_unique($permitted_states);
  }

  /**
   * {@inheritDoc}
   */
  public function calculateDependencies() {
    $dependencies = parent::calculateDependencies();
    /** @var \Drupal\workflows\WorkflowInterface $workflow */
    foreach ($this->workflowStorage->loadMultiple() as $workflow) {
      $type_settings = $workflow->get('type_settings');
      if (array_key_exists('entity_types', $type_settings) && array_key_exists($this->getEntityType(), $type_settings['entity_types'])) {
        $dependencies[$workflow->getConfigDependencyKey()][] = $workflow->getConfigDependencyName();
      }
    }

    return $dependencies;
  }

}
