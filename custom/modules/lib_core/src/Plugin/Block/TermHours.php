<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Allows to place term hours inside the sidebar.
 *
 * @Block(
 *   id = "term_hours_block",
 *   admin_label = @Translation("Hours (Term)"),
 *   category = @Translation("UNB Libraries"),
 * )
 */
class TermHours extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $body = $config['body'] ?? '';

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
      '#default_value' => $config['body'] ?? '',
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
