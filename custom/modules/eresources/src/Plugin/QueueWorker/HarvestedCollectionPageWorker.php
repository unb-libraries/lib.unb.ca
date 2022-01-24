<?php

namespace Drupal\eresources\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\oclc_api\Oclc\OclcAuthorizationInterface;
use Drupal\oclc_api\Plugin\oclc\OclcApiManagerInterface;
use Drupal\oclc_api\Plugin\oclc\OclcPluginManagerTrait;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

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
    $collection_id = $data['#collection_id'];
    unset($data['#collection_id']);

    $now = new DrupalDateTime();
    $collection = \Drupal::entityTypeManager()->getStorage('eresources_harvested_collection')->load($collection_id);
    $collection->set('last_sync', $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT));
    $collection->save();

    $storage = \Drupal::entityTypeManager()->getStorage('eresources_record');

    $api = $this->oclcApi('worldcat_knowledge_base', ['authorization' => $this->oclcAuthorization]);
    $result = $api->get('search-entries', $data);

    if (!empty($result->entries)) {
      foreach ($result->entries as $entry) {
        if (!property_exists($entry, 'kb:entry_uid')) {
          continue;
        }

        $kb_fields = [
          'title', 'kb:entry_uid', 'kb:oclcnum', 'kb:coverage', 'kb:coverageenum', 'kb:publisher',
          'kb:isbn', 'kb:issn', 'kb:eissn', 'kb:coverage_notes', 'kb:collection_user_notes', 'kb:location',
          'kb:author',
        ];

        $fields = [
          'status' => TRUE,
          'collection_id' => $collection_id,
        ];

        foreach ($kb_fields as $key) {
          $local = str_replace('kb:', '', $key);
          $fields[$local] = $entry->{$key} ?? NULL;
        };

        foreach ($entry->links as $link) {
          if ($link->rel == 'via') {
            $fields['url'] = $link->href;
            break;
          }
        }

        // Add or update record.
        $query = $storage->getQuery();
        $ids = $query->condition('entry_uid', $entry->{'kb:entry_uid'})->execute();
        if (!empty($ids)) {
          $id = reset($ids);
          $entity = $storage->load($id);
          foreach ($fields as $key => $value) {
            $entity->set($key, $value);
          }
        }
        else {
          $fields['kb_data_type'] = $collection->getKbDataType();
          $entity = $storage->create($fields);
        }
        $entity->save();

      }
    }
  }

}
