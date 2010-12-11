<?php 

function loginza_settings($form_state) {
  $form = array();
  $form['comments'] = array(
    '#type' => 'fieldset',
    '#title' => t('Comments'),
    '#collapsible' => 1
  );
  $form['comments']['loginza_name_icon'] = array(
    '#type' => 'checkbox',
    '#title' => t('Insert provider icon before user name'),
    '#default_value' =>  variable_get('loginza_name_icon', 0),
  );
  $form['comments']['loginza_picture_icon'] = array(
    '#type' => 'checkbox',
    '#title' => t('Insert provider icon after users\'s avatar'),
    '#default_value' =>  variable_get('loginza_picture_icon', 0),
  );
  return system_settings_form($form);
}