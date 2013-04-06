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
 * @version beta 4
 */

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'myfbconnect.php');
define('ALLOWABLE_PAGE', 'fblogin,fbregister,do_fblogin');

require_once  "./global.php";

$lang->load('myfbconnect');

/* API LOAD */
try {
	include_once MYBB_ROOT . "myfbconnect/src/facebook.php";
}
catch (Exception $e) {
	error_log($e);
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
	
	if($mybb->user['uid']) {
		error($lang->myfbconnect_error_alreadyloggedin);
	}
	
	$do_loginUrl = "/myfbconnect.php?action=do_fblogin";
	myfbconnect_login($do_loginUrl);
}

// don't stop the magic
if ($mybb->input['action'] == "do_fblogin") {
	
	// user detected, just tell him he his already logged in
	if($mybb->user['uid']) {
		error($lang->myfbconnect_error_alreadyloggedin);
	}
	
	// get the user
	$user = $facebook->getUser();
	if ($user) {
		// user found and logged in
		try {
			// get the user public data
			$userdata = $facebook->api("/me?fields=id,name,email,cover,birthday,website,gender,bio,location");
			// let our handler do all the hard work
			myfbconnect_run($userdata);
		}
		// user found, but permissions denied
		catch (FacebookApiException $e) {
			error($lang->myfbconnect_error_noauth);
		}
	}
	else {
		error($lang->myfbconnect_error_noauth);
	}
}

// don't stop the magic
if ($mybb->input['action'] == "fbregister") {
	
	// get the user
	$user = $facebook->getUser();
	if (!$user) {
		error($lang->myfbconnect_error_noauth);
	}
	else {
		try {
			// get the user public data
			$userdata = $facebook->api("/me?fields=id,name,email,cover,birthday,website,gender,bio,location");
		}
		// user found, but permissions denied
		catch (FacebookApiException $e) {
			error($lang->myfbconnect_error_noauth);
		}
	}
	
	// came from our reg page
	if($mybb->request_method == "post") {
		$newuser = array();	
		$newuser['name'] = $mybb->input['username'];
		$newuser['email'] = $userdata['email'];
	}
	
	// user detected, just tell him he his already logged in
	if($mybb->user['uid']) {
		error($lang->myfbconnect_error_alreadyloggedin);
	}
			
	// output our page
	eval("\$fbregister = \"".$templates->get("myfbconnect_register")."\";");
	output_page($fbregister);
}

if(!$mybb->input['action']) {
	header("Location: index.php");
}