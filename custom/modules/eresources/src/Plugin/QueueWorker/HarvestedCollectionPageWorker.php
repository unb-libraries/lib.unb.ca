<?php

namespace Drupal\eresources\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\oclc_api\Oclc\OclcAuthorizationInterface;
use Drupal\oclc_api\Plugin\oclc\OclcApiManagerInterface;
use Drupal\oclc_api\Plugin\oclc\OclcPluginManagerTrait;

/**
 * HarvestedCollectionPageWorker synchronize entries from a KB collection page.
 *
 * @QueueWorker(
 *   id = "eresources_harvested_collection_page",
 *   title = @Translation("eResources Harvested Collection Page"),
 *   cron = {
 *     "time" = 15
 *   }
 * )
 */
class HarvestedCollectionPageWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  use OclcPluginManagerTrait;

  /**
   * An OCLC authorizer.
   *
   * @var \Drupal\oclc_api\Oclc\OclcAuthorizationInterface
   */
  protected $oclcAuthorization;

  /**
   * Class constructor.
   */
  public function __construct(array $configuration, string $plugin_id, $plugin_definition, OclcApiManagerInterface $oclc_api_manager, OclcAuthorizationInterface $oclc_authorizer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->oclcApiManager = $oclc_api_manager;
    $this->oclcAuthorization = $oclc_authorizer;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $oclc_api_manager = $container->get('plugin.manager.oclc_api');
    $oclc_authorizer = $container->get('oclc_authorizer.worldcat_knowledge_base');
    return new static($configuration, $plugin_id, $plugin_definition, $oclc_api_manager, $oclc_authorizer);
  }

  /**
   * {@inheritDoc}
   */
  public function processItem($data) {
    $api = $this->oclcApi('worldcat_knowledge_base', ['authorization' => $this->oclcAuthorization]);
    $result = $api->get('search-entries', $data);

    if (!empty($result->entries)) {
      foreach ($result->entries as $entry) {
        if (!property_exists($entry, 'kb:entry_uid')) {
          continue;
        }

        // Add or update record.
      }
    }
  }

}
