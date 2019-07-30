<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Allows to place static text as a block.
 *
 * @Block(
 *   id = "sidebar_term_hours_block",
 *   admin_label = @Translation("Hours (Term)"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class SidebarHoursTerm extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $body = isset($config['body']) ? $config['body'] : '';

    return [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => $body,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $form['body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Body'),
      '#description' => $this->t('The content to display'),
      '#default_value' => isset($config['body']) ? $config['body'] : '',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['body'] = $values['body'];
  }

}
