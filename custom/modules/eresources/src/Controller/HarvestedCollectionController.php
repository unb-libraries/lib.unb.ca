<?php

namespace Drupal\eresources\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\eresources\Entity\HarvestedCollection;
use Drupal\oclc_api\Oclc\OclcAuthorizationInterface;
use Drupal\oclc_api\Plugin\oclc\OclcApiManagerInterface;
use Drupal\oclc_api\Plugin\oclc\OclcPluginManagerTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides route responses for the lib_core module.
 */
class HarvestedCollectionController extends ControllerBase {

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
  public function __construct(OclcApiManagerInterface $oclc_api_manager, OclcAuthorizationInterface $oclc_authorizer) {
    $this->oclcApiManager = $oclc_api_manager;
    $this->oclcAuthorization = $oclc_authorizer;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    $oclc_api_manager = $container->get('plugin.manager.oclc_api');
    $oclc_authorizer = $container->get('oclc_authorizer.worldcat_knowledge_base');
    return new static($oclc_api_manager, $oclc_authorizer);
  }

  /**
   * Retrieve the OCLC authorizer.
   *
   * @return \Drupal\oclc_api\Oclc\OclcAuthorizationInterface
   *   An oclc authorizer.
   */
  protected function oclcAuthorization() {
    return $this->oclcAuthorization;
  }

  /**
   * Sychronize remote KB collection with local data.
   */
  public function synchronize(HarvestedCollection $eresources_harvested_collection) {
    $api = $this->oclcApi('worldcat_knowledge_base', ['authorization' => $this->oclcAuthorization()]);
    $queue = \Drupal::queue('eresources_harvested_collection_page');
    $perPage = 50;
    $oclcId = $eresources_harvested_collection->getOclcId();

    $defaultParams = [
      'collection_uid' => $oclcId,
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
          '#collection_id' => $eresources_harvested_collection->id(),
        ];
        $queue->createItem($params);
        $index += $perPage;
      }
    }

    $this->messenger()->addStatus("${total} item(s) from the \"${oclcId}\" collection queued for synchronization");
    return $this->redirect('entity.eresources_harvested_collection.collection');
  }

}
