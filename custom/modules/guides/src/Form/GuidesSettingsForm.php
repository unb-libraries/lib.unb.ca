<?php

namespace Drupal\guides\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Configure settings for guides.
 */
class GuidesSettingsForm extends ConfigFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   An entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'guides_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'guides.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('guides.settings');
    $storage = $this->entityTypeManager->getStorage('guide');

    $start = NULL;
    if (!empty($config->get('start_guide'))) {
      $start = $storage->load($config->get('start_guide'));
    }

    $form['getting_started_guide'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'guide',
      '#title' => $this->t('Getting Started Guide'),
      '#default_value' => $start,
    ];

    $help = NULL;
    if (!empty($config->get('help_guide'))) {
      $start = $storage->load($config->get('help_guide'));
    }

    $form['help_guide'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'guide',
      '#title' => $this->t('Help Guide'),
      '#default_value' => $help,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('guides.settings')
      ->set('start_guide', $form_state->getValue('start_guide'))
      ->set('help_guide', $form_state->getValue('help_guide'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
