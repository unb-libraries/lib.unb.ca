<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use OCLC\Auth\WSKey;
use OCLC\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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
    'HILRESERVE: Reserve Collection 7' => 'Harriet Irving Library (Fredericton) - 7 day loan',
    'HIL-MISC' => 'Harriet Irving Library (Fredericton) - 2 hour loan',
    'ENG-MISC' => 'Engineering Library (Fredericton) - 2 hour loan',
    'SCI-MISC1D' => 'Science Library (Fredericton) - 1 day loan',
    'SCI-MISC:' => 'Science Library (Fredericton) - 2 hour loan',
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
    include '/app/html/sites/all/settings/settings.oclc-api.inc';
    $wskey = new WSKey($oclcApiWskey, $oclcApiSecret);

    $inst = '133054';
    $ocn = '807200481';
    $url = "https://worldcat.org/circ/availability/sru/service?x-registryId={$inst}&query=no:ocm{$ocn}";

    $user = new User($inst, '1f918243-4dc6-4d54-a44a-4600117310c0', 'urn:oclc:wms:da');
    $options = ['user' => $user];

    $authorizationHeader = $wskey->getHMACSignature('GET', $url, $options);

    $client = new Client();
    $headers = ['Authorization' => $authorizationHeader];

    try {
      $response = $client->request('GET', $url, ['headers' => $headers]);
      $xml = new \SimpleXMLElement((string) $response->getBody());
      $holdings = $xml->records->record->recordData->opacRecord->holdings;

      $locationMap = $this->locationMap;
      $re = implode('|', array_keys($locationMap));
      $locations = [];

      foreach ($holdings->holding as $holding) {
        preg_match("/($re)/", (string) $holding->shelvingLocation, $matches);
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
      foreach ($locations as $location => $info) {
        $name = array_key_exists($location, $locationMap) ? $locationMap[$location] : $location;
        $status = "<strong>${name}:</strong> " . $info['avail'] . ' of ' . $info['total'] . ' available ';
        if ($info['avail'] == 0) {
          $status .= '(next available ' . date('Y-m-d h:i A', strtotime($info['next'])) . ')';
        }
        $availability[] = ['#markup' => $status];
      }

      return $availability;
    }
    catch (RequestException $error) {
      return ['Unable to get laptop availability at this time.'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    // Disable caching for this block.
    return 0;
  }

}
