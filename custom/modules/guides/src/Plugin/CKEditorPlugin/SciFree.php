<?php

namespace Drupal\guides\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\ckeditor\CKEditorPluginCssInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "SciFree" plugin.
 *
 * @CKEditorPlugin(
 *   id = "scifree",
 *   label = @Translation("SciFree"),
 *   module = "guides"
 * )
 */
class SciFree extends CKEditorPluginBase implements CKEditorPluginCssInterface {

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
    return \Drupal::service('extension.list.module')->getPath('guides') . '/js/plugins/scifree/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      'Scifree' => [
        'label' => $this->t('SciFree'),
        'image' => \Drupal::service('extension.list.module')->getPath('guides') . '/js/plugins/scifree/icons/scifree.png',
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
      \Drupal::service('extension.list.module')->getPath('guides') . '/css/ckeditor-scifree.css',
    ];
  }

}
