<?php
namespace Drupal\lib_core\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Form submission handler.
 *
 * @WebformHandler(
 *   id = "docdel",
 *   label = @Translation("Document Delivery"),
 *   category = @Translation("Webform Handler"),
 *   description = @Translation("Munge query string for document delivery"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 * )
 */

class DocdelWebformHandler extends WebformHandlerBase {

  const DOCDEL_URL = 'https://unb.account.worldcat.org/account/route/openurl';

  /**
   * The current request object.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RequestStack $request) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    $query = $this->request->getCurrentRequest()->query->all();

    $newQuery = [];
    foreach ($query as $k => $v) {
      if (
        !preg_match('/^(ctx|url)/', $k)
        && !preg_match('/(_id|_fmt|_ref|_dat)$/', $k)
      ) {
        $k = str_replace('_', '.', $k);
      }
      $newQuery[$k] = $v;
    }
    $redirect = self::DOCDEL_URL . '?' . http_build_query($newQuery);
    $response = new TrustedRedirectResponse($redirect);
    $form_state->setResponse($response);
    return true;
  }

}
