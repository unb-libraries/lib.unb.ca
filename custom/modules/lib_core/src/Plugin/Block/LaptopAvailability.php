<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\oclc_api\Oclc\OclcAuthorizationInterface;
use Drupal\oclc_api\Plugin\oclc\OclcApiManagerInterface;
use Drupal\oclc_api\Plugin\oclc\OclcPluginManagerTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the UNB Libraries Laptop Availability Block.
 *
 * @Block(
 *   id = "laptop_availability_block",
 *   admin_label = @Translation("UNB Libraries Laptop Availability"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class LaptopAvailability extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * OCN for laptop availability.
   *
   * @var string
   */
  private $ocn = '807200481';

  /**
   * Location key (for regex) and full name pairs.
   *
   * @var array
   */
  private $locationMap = [
    'HILRESERVE: Reserve Collection 1' => [
      'Harriet Irving Library (Fredericton)',
      '1 day loan',
    ],
    'HILRESERVE: Reserve Collection 3' => [
      'Harriet Irving Library (Fredericton)',
      '3 day loan',
    ],
    'HILRESERVE: Reserve Collection 7' => [
      'Harriet Irving Library (Fredericton)',
      '7 day loan',
    ],
    'HIL-MISC7' => [
      'Harriet Irving Library (Fredericton)',
      '7 day loan',
    ],
    'ENG-MISC: Miscellaneous 2' => [
      'Engineering Library (Fredericton)',
      '2 hour loan',
    ],
    'ENG-MISC: Miscellaneous 7' => [
      'Engineering Library (Fredericton)',
      '7 day loan',
    ],
    'SCI-MISC7D' => [
      'Science Library (Fredericton)',
      '7 day loan',
    ],
    'SCI-MISC1D' => [
      'Science Library (Fredericton)',
      '1 day loan',
    ],
    'SCI-MISC: Miscellaneous 3 day loan' => [
      'Science Library (Fredericton)',
      '3 day loan',
    ],
    'SCI-MISC: Miscellaneous 2 hour loan' => [
      'Science Library (Fredericton)',
      '2 hour loan',
    ],
    'HWK-MISC1D1H' => [
      'Saint John Library (Saint John)',
      '1 day loan',
    ],
    'HWK-MISC7D1H' => [
      'Saint John Library (Saint John)',
      '7 day loan',
    ],
  ];

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
  public function __construct(array $configuration, $plugin_id, $plugin_definition, OclcApiManagerInterface $oclc_api_manager, OclcAuthorizationInterface $oclc_authorizer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->oclcApiManager = $oclc_api_manager;
    $this->oclcAuthorization = $oclc_authorizer;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $oclc_api_manager = $container->get('plugin.manager.oclc_api');
    $oclc_authorizer = $container->get('oclc_authorizer.wms_availability');
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $oclc_api_manager,
      $oclc_authorizer
    );
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
   * {@inheritdoc}
   */
  public function build() {
    $availability = $this->getLaptopAvailability();
    return [
      ['#markup' => '<h2>Current Laptop Availability</h2>'],
      [
        '#type' => 'table',
        '#header' => ['Location', 'Loan Period', 'Availability'],
        '#rows' => $availability,
        '#empty' => 'Unable to get laptop availability at this time.',
        '#attributes' => [
          'class' => ['w-auto'],
        ],
        '#cache' => ['max-age' => 0],
      ],
    ];
  }

  /**
   * Fetch a list of locations and the number of laptops available.
   *
   * @return array
   *   List of locations and laptop availability.
   */
  private function getLaptopAvailability() {
    try {
      $configuration = ['authorization' => $this->oclcAuthorization()];
      $response = $this->oclcApi('wms_availability', $configuration)
        ->get('read', [
          'oclc_id' => $this->ocn,
        ]);

      $xml = new \SimpleXMLElement($response);
      $holdings = $xml->records->record->recordData->opacRecord->holdings;

      $locationMap = $this->locationMap;
      $re = implode('|', array_keys($locationMap));
      $locations = [];

      foreach ($holdings->holding as $holding) {
        $result = preg_match("/($re)/", (string) $holding->shelvingLocation, $matches);
        if (!$result) {
          continue;
        }
        if (str_contains((string) $holding->callNumber, 'Charger')) {
          continue;
        }
        $location = $matches[1];
        if (empty($locations[$location])) {
          $locations[$location] = ['avail' => 0, 'total' => 0, 'next' => ''];
        }
        foreach ($holding->circulations->circulation as $circulation) {
          $locations[$location]['total']++;
          if ((int) $circulation->availableNow['value'] == 1) {
            $locations[$location]['avail']++;
          }
          else {
            $avail = (string) $circulation->availabilityDate;
            if (empty($locations[$location]['next']) || $locations[$location]['next'] > $avail) {
              $locations[$location]['next'] = $avail;
            }
          }
        }
      }

      $availability = [];
      ksort($locations);
      foreach ($locations as $location => $info) {
        $row = $locationMap[$location];
        $status = $info['avail'] . ' of ' . $info['total'] . ' available';
        if ($info['avail'] == 0) {
          $status .= ' (next available ' . date('Y-m-d h:i A', strtotime($info['next'])) . ')';
        }
        $row[] = $status;
        $availability[] = $row;
      }

      return $availability;
    }
    catch (Throwable $error) {
      return NULL;
    }
  }

}
