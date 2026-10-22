<?php

namespace Drupal\guides\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
      $help = $storage->load($config->get('help_guide'));
    }

    $form['help_guide'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'guide',
      '#title' => $this->t('Help Guide'),
      '#default_value' => $help,
    ];

    $form['analytics_property_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Google Analytics 4 Property ID'),
      '#description' => $this->t('The numeric GA4 property ID used for guide stats (Admin &gt; Property details), e.g. 123456789. This is not the "G-" measurement ID.'),
      '#default_value' => $config->get('analytics_property_id'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Accept the "properties/123" form the API uses, store just the number.
    $id = preg_replace('~^properties/~', '', trim($form_state->getValue('analytics_property_id')));
    if ($id !== '' && !ctype_digit($id)) {
      $form_state->setErrorByName('analytics_property_id', $this->t('The GA4 Property ID must be numeric.'));
    }
    $form_state->setValue('analytics_property_id', $id);

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('guides.settings')
      ->set('start_guide', $form_state->getValue('getting_started_guide'))
      ->set('help_guide', $form_state->getValue('help_guide'))
      ->set('analytics_property_id', $form_state->getValue('analytics_property_id'))
      ->clear('analytics_view_id')
      ->save();

    \Drupal::cache('menu')->invalidateAll();
    \Drupal::service('plugin.manager.menu.link')->rebuild();

    parent::submitForm($form, $form_state);
  }

}
