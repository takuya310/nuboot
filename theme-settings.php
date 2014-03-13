<?php
/**
 * @file
 * Theme setting callbacks for the nuboot theme.
 */

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function nuboot_form_system_theme_settings_alter(&$form, &$form_state) {
  // Colors fieldset.
  $form['colors'] = array(
    '#type' => 'fieldset',
    '#title' => t('Colors'),
    '#group' => 'general',
  );
  $form['colors']['color_primary'] = array(
    '#type' => 'textfield',
    '#title' => 'Primary Color',
    '#default_value' => '#0062A0',
  );
  $form['colors']['color_secondary'] = array(
    '#type' => 'textfield',
    '#title' => 'Secondary Color',
    '#default_value' => '#D3AE7E',
  );
  // Hero fieldset.
  $form['hero'] = array(
    '#type' => 'fieldset',
    '#title' => t('Hero'),
    '#group' => 'general',
  );
  // Default path for image.
  $hero_path = theme_get_setting('hero_path');
  if (file_uri_scheme($hero_path) == 'public') {
    $hero_path = file_uri_target($hero_path);
  }
  // Helpful text showing the file name, non-editable.
  $form['hero']['hero_path'] = array(
    '#type' => 'textfield',
    '#title' => 'Path to front page hero region background image',
    '#default_value' => $hero_path,
    '#disabled' => TRUE,
  );
  // Upload field.
  $form['hero']['hero_upload'] = array(
    '#type' => 'file',
    '#title' => 'Upload hero region background image',
    '#description' => 'Upload a new image for the hero background.',
    '#upload_validators' => array(
      'file_validate_extensions' => array('png jpg jpeg'),
    ),
  );
  // Attach custom submit handler to the form.
  $form['#submit'][] = 'nuboot_settings_submit';
}
/**
 * Implements hook_setings_submit().
 */
function nuboot_settings_submit($form, &$form_state) {
  $settings = array();
  // Get the previous value.
  $previous = 'public://' . $form['hero']['hero_path']['#default_value'];
  $file = file_save_upload('hero_upload');
  if ($file) {
    $parts = pathinfo($file->filename);
    $destination = 'public://' . $parts['basename'];
    $file->status = FILE_STATUS_PERMANENT;
    if (file_copy($file, $destination, FILE_EXISTS_REPLACE)) {
      $_POST['hero_path'] = $form_state['values']['hero_path'] = $destination;
      // If new file has a different name than the old one, delete the old.
      if ($destination != $previous) {
        drupal_unlink($previous);
      }
    }
  }
  else {
    // Avoid error when the form is submitted without specifying a new image.
    $_POST['hero_path'] = $form_state['values']['hero_path'] = $previous;
  }
}
