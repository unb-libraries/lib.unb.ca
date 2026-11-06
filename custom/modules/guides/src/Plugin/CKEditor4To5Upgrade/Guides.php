<?php

declare(strict_types=1);

namespace Drupal\guides\Plugin\CKEditor4To5Upgrade;

use Drupal\ckeditor5\HTMLRestrictions;
use Drupal\ckeditor5\Plugin\CKEditor4To5UpgradePluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\filter\FilterFormatInterface;

// Drupal 11 has no CKEditor 4 upgrade path; this only exists on Drupal 10.
if (interface_exists(CKEditor4To5UpgradePluginInterface::class)) {

  /**
   * Maps the guides CKEditor 4 widget buttons to their CKEditor 5 versions.
   *
   * @CKEditor4To5Upgrade(
   *   id = "guides",
   *   cke4_buttons = {
   *     "eresources",
   *     "Wc-search",
   *     "Scifree"
   *   },
   *   cke4_plugin_settings = {
   *     "eresources"
   *   },
   *   cke5_plugin_elements_subset_configuration = {
   *   }
   * )
   *
   * @internal
   *   Plugin classes are internal.
   */
  class Guides extends PluginBase implements CKEditor4To5UpgradePluginInterface {

    /**
     * {@inheritdoc}
     */
    public function mapCKEditor4ToolbarButtonToCKEditor5ToolbarItem(string $cke4_button, HTMLRestrictions $text_format_html_restrictions): ?array {
      return match ($cke4_button) {
        'eresources' => ['eresources'],
        'Wc-search' => ['wcSearch'],
        'Scifree' => ['sciFree'],
        default => throw new \OutOfBoundsException(),
      };
    }

    /**
     * {@inheritdoc}
     */
    public function mapCKEditor4SettingsToCKEditor5Configuration(string $cke4_plugin_id, array $cke4_plugin_settings): ?array {
      if ($cke4_plugin_id !== 'eresources') {
        throw new \OutOfBoundsException();
      }
      return [
        'guides_eresources' => [
          'target_entity' => $cke4_plugin_settings['target_entity'] ?? 'guide',
          'resource_type' => (string) ($cke4_plugin_settings['resource_type'] ?? ''),
        ],
      ];
    }

    /**
     * {@inheritdoc}
     */
    public function computeCKEditor5PluginSubsetConfiguration(string $cke5_plugin_id, FilterFormatInterface $text_format): ?array {
      throw new \OutOfBoundsException();
    }

  }

}
