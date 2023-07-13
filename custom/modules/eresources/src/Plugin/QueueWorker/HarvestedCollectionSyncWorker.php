<?php

namespace Drupal\eresources\Plugin\QueueWorker;

use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\oclc_api\Oclc\OclcAuthorizationInterface;
use Drupal\oclc_api\Plugin\oclc\OclcApiManagerInterface;
use Drupal\oclc_api\Plugin\oclc\OclcPluginManagerTrait;
use Drupal\task_ui\Entity\Storage\TaskStorageInterface;
use Drupal\task_ui\Queue\QueueItem;
use Drupal\task_ui\Queue\TaskQueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * HarvestedCollectionSyncWorker synchronize entries from KB collections.
 *
 * @QueueWorker(
 *   id = "eresources_harvested_collection_sync",
 *   title = @Translation("eResources Harvested Collection Synchronization"),
 *   cron = {
 *     "time" = 15
 *   }
 * )
 */
class HarvestedCollectionSyncWorker extends TaskQueueWorkerBase {

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
  public function __construct(array $configuration, string $plugin_id, $plugin_definition, TaskStorageInterface $task_storage, EventDispatcherInterface $dispatcher, OclcApiManagerInterface $oclc_api_manager, OclcAuthorizationInterface $oclc_authorizer, LoggerChannelInterface $logger = NULL) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $task_storage, $dispatcher, $logger);
    $this->oclcApiManager = $oclc_api_manager;
    $this->oclcAuthorization = $oclc_authorizer;
  }

  /**
   * {@inheritDoc}
   */
  protected static function dependencies(ContainerInterface $container) {
    $dependencies = parent::dependencies($container);
    $dependencies[] = $container->get('plugin.manager.oclc_api');
    $dependencies[] = $container->get('oclc_authorizer.worldcat_knowledge_base');
    return $dependencies;
  }

  /**
   * {@inheritDoc}
   */
  public function run(QueueItem $item) {
    $api = $this->oclcApi('worldcat_knowledge_base', ['authorization' => $this->oclcAuthorization]);
    $queue = \Drupal::queue('eresources_harvested_collection_page');
    $perPage = 50;

    $collections = \Drupal::entityTypeManager()->getStorage('eresources_harvested_collection')->loadMultiple();
    foreach ($collections as $collection) {
      $defaultParams = [
        'collection_uid' => $collection->getOclcId(),
        'content' => 'fulltext,ebook,video',
        'itemsPerPage' => $perPage,
      ];

      $result = $api->get('search-entries', $defaultParams + [
        'startIndex' => 1,
      ]);

      $total = $result->{'os:totalResults'};
      if ($total != 0) {
        $queue->createQueue();
        $index = 1;
        while ($index <= $total) {
          $params = $defaultParams + [
            'startIndex' => $index,
            '#collection_id' => $collection->id(),
          ];
          $queue->createItem($params);
          $index += $perPage;
        }
      }
    }
  }

}
