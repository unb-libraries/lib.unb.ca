<?php

namespace Drupal\guides\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableInterface;
use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableTrait;
use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\editor\EditorInterface;

/**
 * CKEditor 5 e-Resources list plugin.
 *
 * Replaces the CKEditor 4 "eresources" widget plugin, keeping its settings.
 */
class Eresources extends CKEditor5PluginDefault implements CKEditor5PluginConfigurableInterface {

  use CKEditor5PluginConfigurableTrait;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'target_entity' => 'guide',
      'resource_type' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['target_entity'] = [
      '#type' => 'select',
      '#title' => $this->t('Target Entity Type'),
      '#description' => $this->t('Setting this to "Category" will limit resource selection to what has been selected in guides in this category'),
      '#options' => [
        'guide' => $this->t('Guide'),
        'category' => $this->t('Category'),
      ],
      '#default_value' => $this->configuration['target_entity'],
    ];
    $form['resource_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Limit e-Resource selection'),
      '#options' => [
        '' => $this->t('Any'),
        'REF' => $this->t('References'),
        'DATA' => $this->t('Databases'),
      ],
      '#default_value' => $this->configuration['resource_type'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['target_entity'] = $form_state->getValue('target_entity');
    $this->configuration['resource_type'] = (string) $form_state->getValue('resource_type');
  }

  /**
   * {@inheritdoc}
   */
  public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array {
    $static_plugin_config['guidesEresources'] += [
      'dialogURL' => Url::fromRoute('guides.eresources_dialog')->toString(),
      'targetEntity' => $this->configuration['target_entity'],
      'resourceType' => $this->configuration['resource_type'],
    ];
    return $static_plugin_config;
  }

}
