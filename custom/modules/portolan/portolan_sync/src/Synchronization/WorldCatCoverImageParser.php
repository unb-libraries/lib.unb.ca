<?php

namespace Drupal\portolan_sync\Synchronization;

/**
 * Parses WorldCat HTML for cover image URIs.
 *
 * @package Drupal\portolan_sync\Synchronization
 */
class WorldCatCoverImageParser implements ParserInterface {

  /**
   * The parser configuration.
   *
   * @var array
   */
  protected $configuration;

  /**
   * Get the parser configuration.
   *
   * @return array
   *   An array of configuration keys and values to customize parsing.
   *   The following configuration is supported:
   *   - regex: (string) the regular experession that identifies a cover image
   *   within an HTML formatted string.
   *   - width: (string) the default width (in pixel) of the retrieved image.
   *   - target_width: (string) the width (in pixel) to which the retrieved
   *   image should be converted.
   */
  protected function getConfiguration() {
    return $this->configuration;
  }

  /**
   * Construct a new WorldCatCoverImageParser instance.
   *
   * @param array $configuration
   *   The parser configuration.
   */
  public function __construct(array $configuration) {
    $this->configuration = $configuration;
  }

  /**
   * Get the regular expression to identify the image URI.
   *
   * @return string
   *   A RegEx formatted string.
   */
  protected function getRegex() {
    return $this->getConfiguration()['regex'];
  }

  /**
   * The default image width.
   *
   * @return string
   *   A numeric string.
   */
  protected function getWidth() {
    return $this->getConfiguration()['width'];
  }

  /**
   * The target image width.
   *
   * @return string
   *   A numeric string.
   */
  protected function getTargetWidth() {
    return $this->getConfiguration()['target_width'];
  }

  /**
   * {@inheritDoc}
   */
  public function parse($data) {
    preg_match($this->getRegex(), $data, $matches);
    if ($matches) {
      $width = $this->getWidth();
      $target_width = $this->getTargetWidth();
      return 'http:' . str_replace("_{$width}", "_{$target_width}", $matches[1]);
    }
    return FALSE;
  }

}
