<?php

namespace Drupal\lib_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\guides\Controller\CourseLinkController;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides route responses for the lib_core module.
 */
class LmsWidgetController extends ControllerBase {

  /**
   * Guzzle\Client instance.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * {@inheritdoc}
   */
  public function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client')
    );
  }

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function lmsWidgetPage() {
    $render = [
      '#is_post' => TRUE,
      '#theme' => 'lms_widget',
      '#attached' => [
        'library' => [
          'lib_core/lms-widget',
        ],
      ],
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    if (empty($_POST)) {
      $render['#is_post'] = FALSE;
      return $render;
    }

    $render['#debug_data'] = $_POST;
    $login = trim($_POST['ext_d2l_username']);
    $sourcedid = trim($_POST['lis_course_section_sourcedid']);
    $label = trim($_POST['context_label']);

    $reserves = $this->getReservesInfo($sourcedid, $login);
    $guides = [];

    if (!empty($reserves)) {
      $match = str_replace([' ', '/'], ['_', ''], $reserves['coursecode']);
      $guides = $this->getGuidesInfo($match);
    }
    elseif (preg_match('/D2L_([^_]+)_[^_]+_([^_]+)_([^_]+)_([^_]+)/', $label, $matches)) {
      list (, $term, $prefix, $number, $section) = $matches;
      $match = "${term}_${prefix}*${number}*${section}";
      $guides = $this->getGuidesInfo($match);
    }
    else {
      $guides = $this->getGuidesInfo($label);
    }

    $chat = [
      'status' => _unb_libraries_askus_check_presence('askus')
    ];

    if (in_array($chat['status'], ['away', 'dnd'])) {
      $chat['note'] = 'Ask Us is currently busy. Please try agin later.';
    }
    elseif (!in_array($chat['status'],['available', 'chat'])) {
      $chat['note'] = _unb_libraries_askus_get_offline_note();
    }

    $render['#chat'] = $chat;
    $render['#guides'] = $guides;
    $render['#reserves'] = $reserves;

    return $render;
  }

  /**
   * Fetch info from the reserves app.
   *
   * @return array
   *   Reserves info.
   */
  private function getReservesInfo($ilpId, $login) {
    $url = 'https://reserves.lib.unb.ca/d2l';
    $params = [
      'id' => $ilpId,
      'login' => $login,
    ];

    try {
      $response = $this->httpClient->post($url, ['form_params' => $params]);
      $json_data = (string) $response->getBody();
      return json_decode($json_data, TRUE);
    }
    catch (\GuzzleHttp\Exception\RequestException $e) {
      return [];
    }
  }

  /**
   * Fetch info from the guides app.
   *
   * @return array
   *   Guides info.
   */
  private function getGuidesInfo($label) {
    $guides = new CourseLinkController();
    return $guides->findLmsMatch($label);
  }

}
