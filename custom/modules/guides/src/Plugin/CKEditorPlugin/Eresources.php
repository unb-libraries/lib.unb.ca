<?php

namespace Drupal\guides\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\ckeditor\CKEditorPluginConfigurableInterface;
use Drupal\ckeditor\CKEditorPluginCssInterface;
use Drupal\Core\Form\FormStateInterface;
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
class Eresources extends CKEditorPluginBase implements CKEditorPluginConfigurableInterface, CKEditorPluginCssInterface {

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
    $settings = $editor->getSettings();

    return [
      'eresources' => [
        'target_entity' => $settings['plugins']['eresources']['target_entity'] ?? 'guide',
        'resource_type' => $settings['plugins']['eresources']['resource_type'] ?? NULL,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    $settings = $editor->getSettings();

    $form['target_entity'] = [
      '#type' => 'select',
      '#title' => $this->t('Target Entity Type'),
      '#description' => $this->t('Setting this to "Category" will limit resource selection to what has been selected in guides in this category'),
      '#options' => [
        'guide' => $this->t('Guide'),
        'category' => $this->t('Category'),
      ],
      '#default_value' => $settings['plugins']['eresources']['target_entity'] ?? 'guide',
    ];

    $form['resource_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Limit e-Resource selection'),
      '#options' => [
        '' => 'Any',
        'REF' => $this->t('References'),
        'DATA' => $this->t('Databases'),
      ],
      '#default_value' => $settings['plugins']['eresources']['resource_type'] ?? NULL,
    ];

    return $form;
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
