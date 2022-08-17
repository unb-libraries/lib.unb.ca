<?php

namespace Drupal\guides\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\ckeditor\CKEditorPluginCssInterface;
use Drupal\editor\Entity\Editor;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines the "WorldCat Search" plugin.
 *
 * @CKEditorPlugin(
 *   id = "wc-search",
 *   label = @Translation("WorldCat Search"),
 *   module = "guides"
 * )
 */
class WcSearch extends CKEditorPluginBase implements CKEditorPluginCssInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return [];
  }

  /**
   * Implements \Drupal\ckeditor\Plugin\CKEditorPluginInterface::isInternal().
   */
  public function isInternal() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'guides') . '/js/plugins/wc-search/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      'Wc-search' => [
        'label' => $this->t('WorldCat Search'),
        'image' => drupal_get_path('module', 'guides') . '/js/plugins/wc-search/icons/wc-search.png',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCssFiles(Editor $editor) {
    return [];
  }

}
