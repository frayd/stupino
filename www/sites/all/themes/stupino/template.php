<?php
/**
 * Initialize theme settings.
 */
if (is_null(theme_get_setting('acoffee_rss_ico'))) {
  global $theme_key;

  /*
   * The default values for the theme variables. Make sure $defaults exactly
   * matches the $defaults in the theme-settings.php file.
   */
  $defaults = array(
    'acoffee_rss_ico' => 1,
    'acoffee_twitter_ico' => '',
  );

  // Get default theme settings.
  $settings = theme_get_settings($theme_key);
  // Don't save the toggle_node_info_ variables.
  if (module_exists('node')) {
    foreach (node_get_types() as $type => $name) {
      unset($settings['toggle_node_info_' . $type]);
    }
  }
  // Save default theme settings.
  variable_set(
    str_replace('/', '_', 'theme_'. $theme_key .'_settings'),
    array_merge($defaults, $settings)
  );
  // Force refresh of Drupal internals.
  theme_get_setting('', TRUE);
}

/**
 * theme_node_submitted
 */
function stupino_node_submitted($node) {
  return t('Written by !username',
    array(
      '!username' => theme('username', $node),
    ));
}

/**
 * theme_comment_submitted
 */
function stupino_comment_submitted($comment) {
  return t('Written by !username on @datetime.',
    array(
      '!username' => theme('username', $comment),
      '@datetime' => format_date($comment->timestamp)
    ));
}

/**
 * theme_feed_icon()
 */
function stupino_feed_icon($url, $title) {
  return '';  
}

/**
 * Implementation of theme_preprocess_page().
 */
function stupino_preprocess_page(&$variables) {
  $icons = '';

  if (theme_get_setting('acoffee_rss_ico') == 1) {
    $icons .= l(theme('image', drupal_get_path('theme', 'stupino') . '/img/acoffee_rss.png', t('RSS Feed'), t('RSS Feed')), 'rss.xml', array('html' => TRUE));
  }

  $twit = theme_get_setting('acoffee_twitter_ico');
  if ($twit != '') {
    $icons .= l(theme('image', drupal_get_path('theme', 'stupino') . '/img/acoffee_twitter.png', t('Twitter'), t('Twitter')), 'http://twitter.com/'. check_plain($twit), array('html' => TRUE));
  }
  
  $variables['rss_twit'] = $icons;
}

/**
 * Implementation of theme_preprocess_node().
 */
function stupino_preprocess_node(&$variables) {
  $node_date = 
    '<div class="create-d">'. date('d', $variables['node']->created) .'</div>'.
    '<div class="create-m">'. date('M', $variables['node']->created) .'</div>'.
    '<div class="create-y">'. date('Y', $variables['node']->created) .'</div>';
  $variables['node_create_date'] = $node_date;
}

/**
 * Implementation of theme_preprocess_block().
 */
function stupino_preprocess_block(&$variables) {
  if (!in_array($variables['block']->region,
    array(
      'topright',
      'right',
    )
  )) {
    $variables['template_files'][] = 'block-clear';
  }
}