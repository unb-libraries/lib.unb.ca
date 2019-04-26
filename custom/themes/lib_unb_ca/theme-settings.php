<?php

/**
 * @file
 * Functions to support theming in the UNB Libraries theme.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function lib_unb_ca_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {
  // Hide Appearance > Settings > Bootstrap Settings > Affix+Layout > [Sidebar items].
  $form['affix']['sidebar_first']['#access'] = FALSE;
  $form['affix']['sidebar_second']['#access'] = FALSE;
  $form['layout']['sidebar_position']['#access'] = FALSE;
  $form['layout']['sidebar_first']['#access'] = FALSE;
  $form['layout']['sidebar_second']['#access'] = FALSE;

}
