<?php

/**
 * @file
 * Functions to support theming in the UNB Libraries theme.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function unb_lib_theme_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {
  // Create a section for unb lib header banner images.
  $form['lib_banner_images'] = [
    '#type' => 'details',
    '#title' => t('Banner Images'),
    '#description' => t('Banner images to be used for in the theme header.'),
    '#weight' => 50,
    '#open' => TRUE,
  ];

  $form['lib_banner_images']['images'] = [
    '#default_value'  => theme_get_setting('images'),
    '#type' => 'managed_file',
    '#upload_location' => 'public://banner-images/',
    '#multiple' => TRUE,
    '#upload_validators' => [
      'file_validate_extensions' => ['jpg'],
      'file_validate_size' => [25600000],
      'file_validate_image_resolution' => ['1170x200'],
    ],
  ];
}
