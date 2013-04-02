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
 * @version beta 1
 */

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'myfbconnect.php');
define('ALLOWABLE_PAGE', 'fblogin,fbregister,do_fblogin');

$templatelist = "myfbconnect_register";

require_once  "./global.php";

$lang->load('myfbconnect');

$appID = $mybb->settings['myfbconnect_appid'];
$appSecret = $mybb->settings['myfbconnect_appsecret'];

// store some defaults
$do_loginUrl = $mybb->settings['bburl'] . "/myfbconnect.php?action=do_fblogin";

// include our API
try {
	include_once MYBB_ROOT . "myfbconnect/src/facebook.php";
}
catch (Exception $e) {
	error_log($e);
}

// Create our application instance
$facebook = new Facebook(array(
	'appId' => $appID,
	'secret' => $appSecret
));

// start all the magic
if ($mybb->input['action'] == "fblogin") {
	
	if($mybb->user['uid']) {
		error($lang->myfbconnect_error_alreadyloggedin);
	}
	
	// empty configuration
	if (empty($appID) OR empty($appSecret)) {
		error($lang->myfbconnect_error_noconfigfound);
	}
	
	if ($mybb->settings['myfbconnect_requestpublishingperms']) {
		$extraPermissions = ", publish_stream";
	}
	
	// get the true login url
	$_loginUrl = $facebook->getLoginUrl(array(
		'scope' => 'user_birthday, user_location, email'.$extraPermissions,
		'redirect_uri' => $do_loginUrl
	));
	
	// redirect to ask for permissions or to login if the user already granted them
	header("Location: " . $_loginUrl);
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
			$userdata = $facebook->api("/me?fields=id,name,email,cover,birthday,website");
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
	
	// user detected, just tell him he his already logged in
	if($mybb->user['uid']) {
		error($lang->myfbconnect_error_alreadyloggedin);
	}
	
	$user = $facebook->getUser();
	if (!$user) {
		error($lang->myfbconnect_error_noauth);
	}
	
	// output our page
	eval("\$fbregister = \"".$templates->get("myfbconnect_register")."\";");
	output_page($fbregister);
}

// if the user comes here directly, or comes with bad parameters, just redirect him
if(!$mybb->input['action']) {
	header("Location: index.php");
}