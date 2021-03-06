<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use GuzzleHttp\Client;

/**
 * Provides the UNB Libraries Laptop Availability Block.
 *
 * @Block(
 *   id = "laptop_availability_block",
 *   admin_label = @Translation("UNB Libraries Laptop Availability"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class LaptopAvailability extends BlockBase {

  /**
   * Location key (for regex) and full name pairs.
   *
   * @var array
   */
  private $locationMap = [
    'HILRESERVE: Reserve Collection 1' => 'Harriet Irving Library (Fredericton) - 1 day loan',
    'HILRESERVE: Reserve Collection 3' => 'Harriet Irving Library (Fredericton) - 3 day loan',
    'HILRESERVE: Reserve Collection 7' => 'Harriet Irving Library (Fredericton) - 7 day loan',
    'HIL-MISC' => 'Harriet Irving Library (Fredericton) - 2 hour loan',
    'ENG-MISC' => 'Engineering Library (Fredericton) - 2 hour loan',
    'SCI-MISC1D' => 'Science Library (Fredericton) - 1 day loan',
    'SCI-MISC: Miscellaneous 3 day loan' => 'Science Library (Fredericton) - 3 day loan',
    'SCI-MISC: Miscellaneous 2 hour loan' => 'Science Library (Fredericton) - 2 hour loan',
    'LAWRESERVE' => 'Law Library (Fredericton) - 6 hour loan',
    'HWK-MISC1D1H' => 'Saint John Library (Saint John) - 1 day loan',
  ];

  /**
   * {@inheritdoc}
   */
  public function build() {
    $availability = $this->getLaptopAvailability();

    $render_array_list = [
      '#title' => 'Current Laptop Availability:',
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#wrapper_attributes' => [
        'id' => 'laptop-availability-wrapper',
        'class' => [
          'alert',
          'alert-success',
          'mt-5',
          'mb-5',
        ],
      ],
      '#attributes' => [
        'class' => ['list-bullets'],
      ],
      '#cache' => ['max-age' => 0],
      '#items' => $availability,
    ];

    return $render_array_list;
  }

  /**
   * Fetch a list of locations and the number of laptops available.
   *
   * @return array
   *   List of locations and laptop availability.
   */
  private function getLaptopAvailability() {
    $oclcApiWskey = '';
    $oclcApiSecret = '';
    include '/app/html/sites/all/settings/settings.oclc-api.inc';
    $apiKey = [
      'clientId' => $oclcApiWskey,
      'clientSecret' => $oclcApiSecret,
    ];
    $inst = '133054';
    $ocn = '807200481';

    try {
      $accessToken = _lib_core_get_oclc_oauth_token(
        $apiKey,
        ["WMS_Availability", "context:{$inst}"]
      );
      $headers = ['Authorization' => "Bearer " . $accessToken->getToken()];
      $url = "https://worldcat.org/circ/availability/sru/service?x-registryId={$inst}&query=no:ocm{$ocn}";

      $client = new Client();
      $response = $client->request('GET', $url, ['headers' => $headers]);
      $xml = new \SimpleXMLElement((string) $response->getBody());
      $holdings = $xml->records->record->recordData->opacRecord->holdings;

      $locationMap = $this->locationMap;
      $re = implode('|', array_keys($locationMap));
      $locations = [];

      foreach ($holdings->holding as $holding) {
        $result = preg_match("/($re)/", (string) $holding->shelvingLocation, $matches);
        if (!$result) {
          $location = (string) $holding->shelvingLocation;
        }
        else {
          $location = $locationMap[$matches[1]];
        }
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
        $status = "<strong>${location}:</strong> " . $info['avail'] . ' of ' . $info['total'] . ' available ';
        if ($info['avail'] == 0) {
          $status .= '(next available ' . date('Y-m-d h:i A', strtotime($info['next'])) . ')';
        }
        $availability[] = ['#markup' => $status];
      }

      return $availability;
    }
    catch (Throwable $error) {
      return ['Unable to get laptop availability at this time.'];
    }
  }

}
