<?php

namespace Drupal\simple_group_path_perms\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class PathPermsSettingsForm.
 */
class PathPermsSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'simple_group_path_perms.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'simple_group_path_perms_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('simple_group_path_perms.settings');

    $form['node_types'] = [
      '#type' => 'select',
      '#title' => $this->t('Control Access To Node Types'),
      '#options' => ['page' => $this->t('page')],
      '#size' => 5,
      '#default_value' => $config->get('node_types'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('simple_group_path_perms.settings')
      ->set('node_types', $form_state->getValue('node_types'))
      ->save();
  }

}
