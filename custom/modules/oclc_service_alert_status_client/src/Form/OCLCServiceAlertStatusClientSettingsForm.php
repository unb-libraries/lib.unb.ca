<?php

namespace Drupal\oclc_service_alert_status_client\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings form for OCLC Service Alert Status Client.
 */
class OCLCServiceAlertStatusClientSettingsForm extends ConfigFormBase {

  const OCLC_SERVICE_ALERT_STATUS__SETTINGS_CLIENT = 'oclc_service_alert_status_client.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'oclc_service_alert_status_client_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return [
      self::OCLC_SERVICE_ALERT_STATUS__SETTINGS_CLIENT,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(self::OCLC_SERVICE_ALERT_STATUS__SETTINGS_CLIENT);

    $base_url = $config->get('base_url');
    $default_base_url = '/oclc-service-alert/status/';
    $form['base_url'] = [
      '#type' => 'textfield',
      '#title' => 'Request URL',
      '#description' => $this->t('URL from which request the oclc service status.'),
      '#default_value' => $base_url ?? $default_base_url,
    ];

    $refresh_interval = $config->get('refresh_interval');
    $default_refresh_interval = 60;
    $form['refresh_interval'] = [
      '#type' => 'number',
      '#title' => 'Refresh Interval',
      '#description' => $this->t('Time interval between refreshes (in seconds).'),
      '#default_value' => isset($refresh_interval) ? $refresh_interval / 1000 : $default_refresh_interval,
    ];

    $message = $config->get('message');
    $default_message = 'We are currently experiencing issues with access to some online resources.';
    $form['message'] = [
      '#type' => 'textarea',
      '#title' => 'Message',
      '#description' => $this->t('Message to be displayed.'),
      '#default_value' => $message ?? $default_message,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $base_url = $form_state->getValue('base_url');
    $refresh_interval = $form_state->getValue('refresh_interval');
    $message = $form_state->getValue('message');
    $this->configFactory->getEditable(OCLC_SERVICE_ALERT_STATUS__SETTINGS_CLIENT)
      ->set('base_url', $base_url)
      ->set('refresh_interval', $refresh_interval * 1000)
      ->set('message', $message)
      ->save();
    parent::submitForm($form, $form_state);
  }

}
