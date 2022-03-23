<?php

namespace Drupal\eresources\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\oclc_api\Oclc\OclcAuthorizationInterface;
use Drupal\oclc_api\Plugin\oclc\OclcApiManagerInterface;
use Drupal\oclc_api\Plugin\oclc\OclcPluginManagerTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\eresources\License;

/**
 * Provides route responses for the lib_core module.
 */
class LicenseController extends ControllerBase {

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
    $oclc_authorizer = $container->get('oclc_authorizer.wms_lman');
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
   * Gets the license title for the display as the page title.
   *
   * @param string $license_id
   *   UUID of the license.
   *
   * @return string
   *   The title.
   */
  public function title($license_id) {
    $license = $this->getLicense($license_id);
    return $license ? 'License: ' . $license->getName() : 'Not Found';
  }

  /**
   * Fetches the license data from the LMAN API.
   *
   * @param string $license_id
   *   UUID of the license.
   *
   * @return mixed
   *   A License object, or NULL if invalid.
   */
  protected function getLicense($license_id) {
    try {
      $api = $this->oclcApi('wms_license_manager', ['authorization' => $this->oclcAuthorization()]);
      $licenseData = $api->get('read', ['license_id' => $license_id]);
      return new License(json_decode($licenseData));
    }
    catch (\Exception $error) {
      return NULL;
    }
  }

  /**
   * Displays a list of licenses.
   *
   * @return array
   *   A simple renderable array.
   */
  public function list() {
    $perPage = 25;
    $pagerManager = \Drupal::service('pager.manager');
    $pagerParameters = \Drupal::service('pager.parameters');
    $page = $pagerParameters->findPage();
    $start = $perPage * $page + 1;

    try {
      $api = $this->oclcApi('wms_license_manager', ['authorization' => $this->oclcAuthorization()]);
      $licensesData = $api->get('list', [
        'itemsPerPage' => $perPage,
        'startIndex' => $start,
        'fetchDetails' => 'false',
      ]);
    }
    catch (\Exception $error) {
      \Drupal::logger('eresources')->error($error);
      return [
        '#markup' => '<div class="alert alert-danger rounded-0">Unable to retrieve licenses at this time. Please try again later.</div>',
      ];
    }
    $licenses = json_decode($licensesData);
    $total = 0;
    foreach ($licenses->extensions as $ext) {
      if ($ext->name == 'os:totalResults') {
        $total = $ext->children[0];
        break;
      }
    }
    $pagerManager->createPager($total, $perPage);

    $results = [];
    foreach ($licenses->entries as $license) {
      $content = $license->content;
      $link = Link::fromTextAndUrl($content->name, Url::fromRoute('eresources.license.view', ['license_id' => $content->id]));
      $results[] = ['#markup' => $link->toString() . ' (' . $content->id . ')'];
    }

    return [
      [
        '#markup' => "<p>Showing licenses $start to " . ($start + count($results) - 1) . " of $total",
      ],
      [
        '#theme' => 'item_list',
        '#list_type' => 'ol',
        '#attributes' => [
          'start' => $start,
        ],
        '#title' => '',
        '#items' => $results,
      ],
      [
        '#type' => 'pager',
      ],
    ];
  }

  /**
   * Displays license data.
   *
   * @return array
   *   A simple renderable array.
   */
  public function view($license_id) {
    $license = $this->getLicense($license_id);
    if (!$license) {
      throw new NotFoundHttpException();
    }
    return [
      '#theme' => 'license',
      '#license' => $license,
    ];
  }

}
