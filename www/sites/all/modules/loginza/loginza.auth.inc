<?php 

function loginza_auth() {
	$token = $_POST['token'];
	if (!$token) {
	  exit();
	}
	$url = 'http://loginza.ru/api/authinfo?token=' . $token;
	$json = @file_get_contents($url) or drupal_set_message(t('Error while connecting to http://loginza.ru'), 'error');
	$vars = function_exists('json_decode') ? json_decode($json, TRUE) : old_php_json_decode($json, TRUE);
	if (!isset($vars['identity'])) {
	  exit();
	}
	$vars['login'] = time();
	$_SESSION['loginza'] = $vars;
	if (isset($_REQUEST['destination'])) {
	   drupal_goto($_REQUEST['destination']);
	}
	else {
  	drupal_goto();
	}
}

function loginza_logout() {
  global $user;
  $module = 'user';
  if (isset($user->loginza) || isset($_SESSION['loginza'])) {
  	unset($user->loginza, $_SESSION['loginza']);
  	$module = 'loginza';
  }
  watchdog($module, 'Session closed for %name.', array('%name' => $user->name));
  session_destroy();
  $null = NULL;
  user_module_invoke('logout', $null, $user);

  // Load the anonymous user
  $user = drupal_anonymous_user();

  drupal_goto();
}


function old_php_json_decode($content, $assoc = FALSE) {
  require_once drupal_get_path('module', 'loginza') . '/JSON.php';
  if ($assoc) {
    $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
  } 
  else {
    $json = new Services_JSON;
  }
  return $json->decode($content);
}