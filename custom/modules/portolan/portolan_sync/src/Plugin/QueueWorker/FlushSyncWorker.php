<?php

namespace Drupal\portolan_sync\Plugin\QueueWorker;

use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\portolan_sync\Synchronization\DataSynchronizerInterface;
use Drupal\task_ui\Entity\Storage\TaskStorageInterface;
use Drupal\task_ui\Queue\QueueItem;
use Drupal\task_ui\Queue\TaskQueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Flushes Portolan records and re-imports from WorldCat.
 *
 * @QueueWorker(
 *   id = "portolan_flush_sync",
 *   title = @Translation("Portolan Sync (Flush)"),
 *   cron = {
 *     "time" = 60
 *   }
 * )
 */
class FlushSyncWorker extends TaskQueueWorkerBase {

  /**
   * The synchronizer.
   *
   * @var \Drupal\portolan_sync\Synchronization\DataSynchronizerInterface
   */
  protected $synchronizer;

  /**
   * Get the synchronizer.
   *
   * @return \Drupal\portolan_sync\Synchronization\DataSynchronizerInterface
   *   A synchronizer object.
   */
  protected function synchronizer() {
    return $this->synchronizer;
  }

  /**
   * Construct a new FlushSyncWorker instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\task_ui\Entity\Storage\TaskStorageInterface $task_storage
   *   A storage handler for task entities.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *   An event dispatcher.
   * @param \Drupal\portolan_sync\Synchronization\DataSynchronizerInterface $synchronizer
   *   The synchronizer.
   * @param \Drupal\Core\Logger\LoggerChannelInterface|null $logger
   *   (optional) A logger channel.
   */
  public function __construct(array $configuration, string $plugin_id, $plugin_definition, TaskStorageInterface $task_storage, EventDispatcherInterface $dispatcher, DataSynchronizerInterface $synchronizer, LoggerChannelInterface $logger = NULL) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $task_storage, $dispatcher, $logger);
    $this->synchronizer = $synchronizer;
  }

  /**
   * {@inheritDoc}
   */
  protected static function dependencies(ContainerInterface $container) {
    $dependencies = parent::dependencies($container);
    $dependencies[] = $container->get('portolan.oclc_synchronizer');
    return $dependencies;
  }

  /**
   * {@inheritDoc}
   */
  public function run(QueueItem $item) {
    $this->synchronizer()->sync();
  }

}
