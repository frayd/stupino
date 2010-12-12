<?php
/**
 * @file
 * Stupino Theme Settings
 */

/**
* Implementation of THEMEHOOK_settings() function.
*
* @param $saved_settings
*   array An array of saved settings for this theme.
* @return
*   array A form array.
*/
function acoffee_settings($saved_settings) {
  /*
   * The default values for the theme variables. Make sure $defaults exactly
   * matches the $defaults in the template.php file.
   */
  $defaults = array(
    'acoffee_twitter_ico' => '',
    'acoffee_rss_ico' => 1,
  );

  // Merge the saved variables and their default values
  $settings = array_merge($defaults, $saved_settings);

  // Create the form widgets using Forms API
  $form['acoffee_rss_ico'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show a special RSS icon.'),
    '#default_value' => $settings['acoffee_rss_ico'],
  );
  $form['acoffee_twitter_ico'] = array(
    '#type' => 'textfield',
    '#title' => t('Twitter account name'),
    '#default_value' => $settings['acoffee_twitter_ico'],
    '#size' => 60,
    '#maxlength' => 156,
    '#description' => t('Enter your twitter account name. E.g. <em>templatestock</em>. '.
      'Leave blank to remove the twitter icon.'),
  );

  // Return the additional form widgets
  return $form;
}