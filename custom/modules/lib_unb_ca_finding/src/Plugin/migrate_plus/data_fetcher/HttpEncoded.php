<?php

namespace Drupal\lib_unb_ca_finding\Plugin\migrate_plus\data_fetcher;

use Drupal\migrate_plus\Annotation\MigratePlusDataFetcher;
use Drupal\migrate_plus\Plugin\migrate_plus\data_fetcher\DataFetcherPluginBase;
use Drupal\lib_unb_ca_finding\Url\UrlEncoder;
use GuzzleHttp\Exception\RequestException;

/**
 * Fetcher that percent-encodes the URL before doing an HTTP GET.
 *
 * @MigratePlusDataFetcher(
 *   id = "http_encoded",
 *   title = "HTTP (encoded)"
 * )
 */
class HttpEncoded extends DataFetcherPluginBase {

  /**
   * The Guzzle HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * HttpEncoded constructor.
   *
   * Uses the container http_client service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = \Drupal::service('http_client');
  }

  /**
   * Fetch the remote resource at $url after encoding it.
   *
   * This method signature matches what migrate_plus expects from data_fetchers:
   * fetch($url) and return the raw response string or NULL on failure.
   *
   * @param string $url
   *   The raw URL from the migration source.
   *
   * @return string|null
   *   Response body string on success, NULL on failure.
   */
  public function fetch($url) {
    $encoded = UrlEncoder::encodeUrl($url);

    // Build options from plugin configuration if present.
    $options = [];

    // Pass headers if provided in the plugin configuration.
    if (!empty($this->configuration['headers']) && is_array($this->configuration['headers'])) {
      $options['headers'] = $this->configuration['headers'];
    }

    // Timeout configuration (seconds).
    if (!empty($this->configuration['timeout'])) {
      $options['timeout'] = (float) $this->configuration['timeout'];
    }

    // Allow user to set other Guzzle options in configuration under 'guzzle_options'.
    if (!empty($this->configuration['guzzle_options']) && is_array($this->configuration['guzzle_options'])) {
      $options = array_merge($options, $this->configuration['guzzle_options']);
    }

    try {
      $response = $this->httpClient->request('GET', $encoded, $options);
      return (string) $response->getBody();
    }
    catch (RequestException $e) {
      // Log and return NULL so migration can continue (migrate_plus typically
      // skips items when fetchers return NULL).
      \Drupal::logger('lib_unb_ca_finding')->error('HTTP fetch failed for @url: @message', [
        '@url' => $encoded,
        '@message' => $e->getMessage(),
      ]);
      return NULL;
    }
  }

}