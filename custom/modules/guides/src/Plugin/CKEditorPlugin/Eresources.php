<?php

namespace Drupal\guides\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\ckeditor\CKEditorPluginCssInterface;
use Drupal\editor\Entity\Editor;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines the "e-Resources" plugin.
 *
 * @CKEditorPlugin(
 *   id = "eresources",
 *   label = @Translation("e-Resources"),
 *   module = "guides"
 * )
 */
class Eresources extends CKEditorPluginBase implements CKEditorPluginCssInterface {

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
    return drupal_get_path('module', 'guides') . '/js/plugins/eresources/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      'eresources' => [
        'label' => $this->t('e-Resources'),
        'image' => drupal_get_path('module', 'guides') . '/js/plugins/eresources/icons/eresources.png',
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
    return [
      drupal_get_path('module', 'guides') . '/css/ckeditor-eresources.css',
    ];
  }

}
