<?php

namespace Drupal\guides\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;

/**
 * Derivative class that provides the menu links guides help.
 */
class HelpMenuLink extends DeriverBase implements ContainerDeriverInterface {

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Creates a HelpMenuLink instance.
   *
   * @param string $base_plugin_id
   *   The base plugin id.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct($base_plugin_id, ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $config = $this->configFactory->get('guides.settings');

    $links = [];

    if ($guide = $config->get('help_guide')) {
      $links['help'] = [
        'title' => 'Help',
        'url' => Url::fromRoute('entity.guide.canonical', ['guide' => $guide])->toUriString(),
      ] + $base_plugin_definition;
    }

    if ($guide = $config->get('start_guide')) {
      $links['start'] = [
        'title' => 'Getting Started',
        'url' => Url::fromRoute('entity.guide.canonical', ['guide' => $guide])->toUriString(),
      ] + $base_plugin_definition;
    }

    return $links;
  }

}
