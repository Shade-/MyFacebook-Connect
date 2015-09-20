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
if ($mybb->settings['disableregs'] == 1) {

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
	
	if ($mybb->user['uid']) {
		error($lang->myfbconnect_error_alreadyloggedin);
	}
	
	// Remember page to ensure we redirect to the previous page after the user logs in
	$FacebookConnect->remember_page();
	$FacebookConnect->authenticate();
	
}

// Receive the incoming data from Facebook and evaluate the user
if ($mybb->input['action'] == 'do_login') {
	
	// Already logged in? You should not use this
	if ($mybb->user['uid']) {
		error($lang->myfbconnect_error_alreadyloggedin);
	}
	
	// Attempt to get an user if authenticated
	$user = $FacebookConnect->get_user();	
	if ($user) {
	
		$process = $FacebookConnect->process($user);
		
		if ($process['error']) {
			$errors = $process['error'];
			$mybb->input['action'] = 'register';
		}
	}
	
}

// Register page fallback
if ($mybb->input['action'] == 'register') {
	
	// Already logged in? You should not use this
	if ($mybb->user['uid']) {
		error($lang->myfbconnect_error_alreadyloggedin);
	}
	
	if (!$FacebookConnect->check_user()) {
		$FacebookConnect->authenticate();
	}
	else {
		$user = $FacebookConnect->get_user();
	}
	
	// Came from our reg page
	if ($mybb->request_method == "post") {
	
		$newuser = array();
		$newuser['name'] = $mybb->input['username'];
		$newuser['email'] = $mybb->input['email'];
		
		$settingsToAdd = array();
		$settingsToCheck = array(
			"fbavatar",
			"fbsex",
			"fbdetails",
			"fbbio",
			"fbbday",
			"fblocation"
		);
		
		foreach ($settingsToCheck as $setting) {
		
			if ($mybb->input[$setting] == 1) {
				$settingsToAdd[$setting] = 1;
			}
			else {
				$settingsToAdd[$setting] = 0;
			}
			
		}
		
		// Register him
		$user = $FacebookConnect->register($newuser);
		
		// Insert options and extra data and login
		if (!$user['error']) {
		
			$db->update_query('users', $settingsToAdd, 'uid = ' . (int) $user['uid']);
			
			// Sync
			$newUser = array_merge($user, $settingsToAdd);
			$FacebookConnect->sync($newUser);
			
			// Login
			$FacebookConnect->login($user);
			
			// Redirect
			$FacebookConnect->redirect($mybb->input['redUrl'], $lang->sprintf($lang->myfbconnect_redirect_title, $user['username']), $lang->myfbconnect_redirect_registered);
		}
		else {
			$errors = inline_error($user['error']);
		}
		
	}
	
	$options = '';
	$settingsToBuild = '';
	
	// Checking if we want to sync that stuff (admin)
	$settingsToCheck = array(
		'fbavatar',
		'fbbday',
		'fbsex',
		'fbdetails',
		'fbbio',
		'fblocation'
	);
	
	foreach ($settingsToCheck as $setting) {
	
		$tempKey = 'myfbconnect_' . $setting;
		
		if ($mybb->settings[$tempKey]) {
			$settingsToBuild[] = $setting;
		}
		
	}
	
	foreach ($settingsToBuild as $setting) {
	
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
	
	$username = "<input type=\"text\" class=\"textbox\" name=\"username\" value=\"{$user['name']}\" />";
	$email = "<input type=\"text\" class=\"textbox\" name=\"email\" value=\"{$user['email']}\" />";
	$redirectUrl = "<input type=\"hidden\" name=\"redUrl\" value=\"{$_SERVER['HTTP_REFERER']}\" />";
	
	// Output our page
	eval("\$fbregister = \"" . $templates->get("myfbconnect_register") . "\";");
	output_page($fbregister);
	
}
