<?php

/**
 * MyFacebook Connect
 * 
 * Integrates MyBB with Facebook, featuring login and registration.
 *
 * @package MyFacebook Connect
 * @page Main
 * @author  Shade <legend_k@live.it>
 * @license http://opensource.org/licenses/mit-license.php MIT license
 * @version 1.1
 */

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'myfbconnect.php');
define('ALLOWABLE_PAGE', 'fblogin,fbregister,do_fblogin');

require_once "./global.php";

$lang->load('myfbconnect');

// master switch is set to off
if (!$mybb->settings['myfbconnect_enabled']) {
	header("Location: index.php");
	exit;
}

// registrations are disabled
if($mybb->settings['disableregs'] == 1) {
	if(!$lang->registrations_disabled) $lang->load("member");
	error($lang->registrations_disabled);
}

/* API LOAD */
try {
	include_once MYBB_ROOT . "myfbconnect/src/facebook.php";
}
catch (Exception $e) {
	error($lang->sprintf($lang->myfbconnect_error_report, $e->getMessage()));
}

$appID = $mybb->settings['myfbconnect_appid'];
$appSecret = $mybb->settings['myfbconnect_appsecret'];

// empty configuration
if (empty($appID) OR empty($appSecret)) {
	error($lang->myfbconnect_error_noconfigfound);
}

// Create our application instance
$facebook = new Facebook(array(
	'appId' => $appID,
	'secret' => $appSecret
));
/* END API LOAD */

// start all the magic
if ($mybb->input['action'] == "fblogin") {
	
	if ($mybb->user['uid']) {
		error($lang->myfbconnect_error_alreadyloggedin);
	}
	
	$loginUrl = "/myfbconnect.php?action=do_fblogin";
	myfbconnect_login($loginUrl);
}

// don't stop the magic
if ($mybb->input['action'] == "do_fblogin") {
	
	// user detected, just tell him he his already logged in
	if ($mybb->user['uid']) {
		error($lang->myfbconnect_error_alreadyloggedin);
	}
	
	// user found and logged in
	try {
		// get the user public data
		$userdata = $facebook->api("/me?fields=id,name,email,cover,birthday,website,gender,bio,location,verified");
		// let our handler do all the hard work
		$magic = myfbconnect_run($userdata);
		if ($magic['error']) {
			$errors = $magic['data'];
			$mybb->input['action'] = "fbregister";
		}
	}
	// user found, but permissions denied
	catch (FacebookApiException $e) {
		error($lang->sprintf($lang->myfbconnect_error_report, $e->getMessage()));
	}
}

// don't stop the magic, again!
if ($mybb->input['action'] == "fbregister") {
	
	// user detected, just tell him he his already logged in
	if ($mybb->user['uid']) {
		error($lang->myfbconnect_error_alreadyloggedin);
	}
	
	// get the user
	$user = $facebook->getUser();
	if (!$user) {
		error($lang->myfbconnect_error_noauth);
	} else {
		try {
			// get the user public data
			$userdata = $facebook->api("/me?fields=id,name,email,cover,birthday,website,gender,bio,location,verified");
		}
		// user found, but permissions denied
		catch (FacebookApiException $e) {
			error($lang->sprintf($lang->myfbconnect_error_report, $e->getMessage()));
		}
	}
	
	// came from our reg page
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
			// variable variables. Yay!
			if ($mybb->input[$setting] == 1) {
				$settingsToAdd[$setting] = 1;
			} else {
				$settingsToAdd[$setting] = 0;
			}
		}
		
		// register it
		$user_info = myfbconnect_register($newuser);
		
		// insert options and extra data
		if ($db->update_query('users', $settingsToAdd, 'uid = ' . (int) $user_info['uid']) AND !($user_info['error'])) {
		
			// compatibility with third party plugins which affects registration (MyAlerts for example)
			$plugins->run_hooks("member_do_register_end");
			
			// update on-the-fly that array of data dude!
			$newUser = array_merge($user_info, $settingsToAdd);
			// oh yeah, let's sync!
			myfbconnect_sync($newUser);
			
			// login the user normally, and we have finished.	
			$db->delete_query("sessions", "ip='" . $db->escape_string($session->ipaddress) . "' AND sid != '" . $session->sid . "'");
			$newsession = array(
				"uid" => $user_info['uid']
			);
			$db->update_query("sessions", $newsession, "sid='" . $session->sid . "'");
			
			// finally log the user in
			my_setcookie("mybbuser", $user_info['uid'] . "_" . $user_info['loginkey'], null, true);
			my_setcookie("sid", $session->sid, -1, true);
			// redirect the user to where he came from
			if ($mybb->input['redUrl'] AND strpos($mybb->input['redUrl'], "action=fblogin") === false AND strpos($mybb->input['redUrl'], "action=fbregister") === false) {
				$redirect_url = htmlentities($mybb->input['redUrl']);
			} else {
				$redirect_url = "index.php";
			}
			redirect($redirect_url, $lang->myfbconnect_redirect_registered, $lang->sprintf($lang->myfbconnect_redirect_title, $user_info['username']));
		} else {
			$errors = $user_info['data'];
		}
	}
	
	if ($errors) {
		$errors = inline_error($errors);
	}
	
	$options = "";
	
	$settingsToBuild = array(
		"fbavatar"
	);
	
	// checking if we want to sync that stuff (admin)
	$settingsToCheck = array(
		"fbbday",
		"fbsex",
		"fbdetails",
		"fbbio",
		"fblocation"
	);
	
	foreach ($settingsToCheck as $setting) {
		$tempKey = 'myfbconnect_' . $setting;
		if ($mybb->settings[$tempKey]) {
			$settingsToBuild[] = $setting;
		}
	}
	
	foreach ($settingsToBuild as $setting) {
		// variable variables. Yay!
		$tempKey = 'myfbconnect_settings_' . $setting;
		$checked = " checked=\"checked\"";
		$label = $lang->$tempKey;
		$altbg = alt_trow();
		eval("\$options .= \"" . $templates->get('myfbconnect_register_settings_setting') . "\";");
	}
		
	// if registration failed, we certainly have some custom inputs, so we have to display them instead of the Facebook ones
	if(!empty($mybb->input['username'])) {
		$userdata['name'] = $mybb->input['username'];
	}
	if(!empty($mybb->input['email'])) {
		$userdata['email'] = $mybb->input['email'];
	}
	
	$username = "<input type=\"text\" class=\"textbox\" name=\"username\" value=\"{$userdata['name']}\" />";
	$email = "<input type=\"text\" class=\"textbox\" name=\"email\" value=\"{$userdata['email']}\" />";
	$redirectUrl = "<input type=\"hidden\" name=\"redUrl\" value=\"{$_SERVER['HTTP_REFERER']}\" />";
	
	// output our page
	eval("\$fbregister = \"" . $templates->get("myfbconnect_register") . "\";");
	output_page($fbregister);
}

if (!$mybb->input['action']) {
	header("Location: index.php");
	exit;
}