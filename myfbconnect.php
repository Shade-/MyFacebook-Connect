<?php

/**
 * MyFacebook Connect
 */

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'myfbconnect.php');
define('ALLOWABLE_PAGE', 'login,do_login,register');

require_once "./global.php";

$lang->load('myfbconnect');

if (!$mybb->settings['myfbconnect_enabled']) {

	header("Location: index.php");
	exit;

}

// Registrations are disabled
if ($mybb->settings['disableregs'] == 1 and !$mybb->settings['myfbconnect_keeprunning']) {

	if (!$lang->registrations_disabled) {
		$lang->load("member");
	}

	error($lang->registrations_disabled);

}

// Load API
require_once MYBB_ROOT . "inc/plugins/MyFacebookConnect/class_facebook.php";
$FacebookConnect = new MyFacebook();

// If the user is watching another page, fallback to login
if (!in_array($mybb->input['action'], explode(',', ALLOWABLE_PAGE))) {
	$mybb->input['action'] = 'login';
}

// Begin the authenticating process
if ($mybb->input['action'] == 'login') {

	// Already logged in? Redirect to the homepage
	if ($mybb->user['uid']) {
		header('Location: index.php');
	}

	$FacebookConnect->authenticate();

}

// Receive the incoming data from Facebook and evaluate the user
if ($mybb->input['action'] == 'do_login') {

	// Already logged in? Redirect to the homepage
	if ($mybb->user['uid']) {
		header('Location: index.php');
	}

	// Save the incoming access token
	$FacebookConnect->save_token();

	// Attempt to get an user if authenticated
	$user = $FacebookConnect->get_user();

	if ($user) {

		$process = $FacebookConnect->process();

		if ($process['error']) {

			$errors = $process['error'];
			$mybb->input['action'] = 'register';

		}

	}

}

// Register page fallback
if ($mybb->input['action'] == 'register') {

	// Already logged in? Redirect to the homepage
	if ($mybb->user['uid']) {
		header('Location: index.php');
	}

	$user = $FacebookConnect->get_user();

	$settingsToCheck = [
		'fbavatar',
		'fbbday',
		'fbsex',
		'fbdetails',
		'fblocation'
	];

	// Came from our reg page
	if ($mybb->request_method == "post") {

		$newuser = [];
		$newuser['name'] = $mybb->input['username'];
		$newuser['email'] = $mybb->input['email'];

		$settingsToAdd = [];

		foreach ($settingsToCheck as $setting) {
			$settingsToAdd[$setting] = ($mybb->input[$setting] == 1) ? 1 : 0;
		}

		// Register him
		$user = $FacebookConnect->register($newuser);

		// Insert options and extra data and login
		if (!$user['error']) {

			$db->update_query('users', $settingsToAdd, 'uid = ' . (int) $user['uid']);

			// Sync
			$FacebookConnect->sync(array_merge($user, $settingsToAdd));

			// Login
			$FacebookConnect->login($user);

			// Redirect
			$FacebookConnect->redirect((string) $mybb->input['redirect_url'], $lang->sprintf($lang->myfbconnect_redirect_title, $user['username']), $lang->myfbconnect_redirect_registered);

		}
		else {
			$errors = inline_error($user['error']);
		}

	}

	$options = '';
	$settings_to_build = [];

	foreach ($settingsToCheck as $setting) {

		$tempKey = 'myfbconnect_' . $setting;

		if ($mybb->settings[$tempKey]) {
			$settings_to_build[] = $setting;
		}

	}

	foreach ($settings_to_build as $setting) {

		$tempKey = 'myfbconnect_settings_' . $setting;
		$checked = " checked=\"checked\"";

		$label = $lang->$tempKey;
		$altbg = alt_trow();
		eval("\$options .= \"" . $templates->get('myfbconnect_register_settings_setting') . "\";");

	}

	// If registration failed, we certainly have some custom inputs, so we have to display them instead of the Facebook ones
	if ($mybb->input['username']) {
		$user['name'] = htmlspecialchars_uni($mybb->input['username']);
	}

	if ($mybb->input['email']) {
		$user['email'] = htmlspecialchars_uni($mybb->input['email']);
	}

	$lang->myfbconnect_register_basic_info = $lang->sprintf($lang->myfbconnect_register_basic_info, $user['id']);

	$redirect_url = $_SERVER['HTTP_REFERER'];

	// Show the registration page
	eval("\$register = \"" . $templates->get("myfbconnect_register") . "\";");
	output_page($register);

}
