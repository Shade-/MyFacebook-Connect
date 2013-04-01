<?php
/**
 * MyFacebook Connect
 * 
 * Integrates MyBB with Facebook, featuring login and registration.
 *
 * @package MyFacebook Connect
 * @author  Shade <legend_k@live.it>
 * @license http://opensource.org/licenses/mit-license.php MIT license
 * @version alpha 0.1
 */

if (!defined('IN_MYBB')) {
	die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');
}

if (!defined("PLUGINLIBRARY")) {
	define("PLUGINLIBRARY", MYBB_ROOT . "inc/plugins/pluginlibrary.php");
}

function myfbconnect_info()
{
	return array(
		'name' => 'MyFacebook Connect',
		'description' => 'Integrates MyBB with Facebook, featuring login and registration.',
		'website' => 'https://github.com/Shade-/MyFacebookConnect',
		'author' => 'Shade',
		'authorsite' => 'http://www.idevicelab.net/forum',
		'version' => 'alpha 1',
		'compatibility' => '16*',
		'guid' => 'none... yet'
	);
}

function myfbconnect_is_installed()
{
	global $cache;
	
	$info = myfbconnect_info();
	$installed = $cache->read("shade_plugins");
	if ($installed[$info['name']]) {
		return true;
	}
}

function myfbconnect_install()
{
	global $db, $PL, $lang, $mybb, $cache;
	
	if (!$lang->myfbconnect) {
		$lang->load('myfbconnect');
	}
	
	if (!file_exists(PLUGINLIBRARY)) {
		flash_message($lang->myfbconnect_pluginlibrary_missing, "error");
		admin_redirect("index.php?module=config-plugins");
	}
	
	$PL or require_once PLUGINLIBRARY;
	
	$PL->settings('myfbconnect', $lang->myfbconnect_settings, $lang->myfbconnect_settings_desc, array(
		'enabled' => array(
			'title' => $lang->myfbconnect_settings_enable,
			'description' => $lang->myfbconnect_settings_enable_desc,
			'value' => '1'
		),
		'appid' => array(
			'title' => $lang->myfbconnect_settings_appid,
			'description' => $lang->myfbconnect_settings_appid_desc,
			'value' => '',
			'optionscode' => 'text'
		),
		'appsecret' => array(
			'title' => $lang->myfbconnect_settings_appsecret,
			'description' => $lang->myfbconnect_settings_appsecret_desc,
			'value' => '',
			'optionscode' => 'text'
		),
		'usergroup' => array(
			'title' => $lang->myfbconnect_settings_usergroup,
			'description' => $lang->myfbconnect_settings_usergroup_desc,
			'value' => '2',
			'optionscode' => 'text'
		)
	));
	
	// insert our Facebook ID column into the database
	$db->query("ALTER TABLE " . TABLE_PREFIX . "users ADD `myfb_uid` bigint(50) NOT NULL");
	
	// create cache
	$info = myfbconnect_info();
	$shadePlugins = $cache->read('shade_plugins');
	$shadePlugins[$info['name']] = array(
		'title' => $info['name'],
		'version' => $info['version']
	);
	$cache->update('shade_plugins', $shadePlugins);
	
	rebuild_settings();
	
}

function myfbconnect_uninstall()
{
	global $db, $PL, $cache, $lang;
	
	if (!$lang->myfbconnect) {
		$lang->load('myfbconnect');
	}
	
	if (!file_exists(PLUGINLIBRARY)) {
		flash_message($lang->myfbconnect_pluginlibrary_missing, "error");
		admin_redirect("index.php?module=config-plugins");
	}
	
	$PL or require_once PLUGINLIBRARY;
	
	$PL->settings_delete('myfbconnect');
	
	// delete our Facebook ID column
	$db->query("ALTER TABLE " . TABLE_PREFIX . "users drop `myfb_uid`");
	
	$info = myfbconnect_info();
	// delete the plugin from cache
	$shadePlugins = $cache->read('shade_plugins');
	unset($shadePlugins[$info['name']]);
	$cache->update('shade_plugins', $shadePlugins);
	// rebuild settings
	rebuild_settings();
}

global $mybb, $settings;

if ($settings['myfbconnect_enabled']) {
	$plugins->add_hook('pre_output_page', 'myfbconnect_pre_output');
	$plugins->add_hook('index_end', 'myfbconnect_index_end');
}

function myfbconnect_pre_output(&$contents)
{
	
	global $mybb, $lang;
	
	if (!$lang->myfbconnect) {
		$lang->load('myfbconnect');
	}
	
	$loginUrl = $mybb->settings['bburl'] . "/index.php?action=fblogin";
	$loginUrl = "<a href=\"{$loginUrl}\">{$lang->myfbconnect_login}</a>";
	
	$contents = str_replace('<facebooklogin>', $loginUrl, $contents);
	
	return $contents;
}

function myfbconnect_index_end()
{
	
	global $mybb, $lang;
	
	if (!$lang->myfbconnect) {
		$lang->load('myfbconnect');
	}
	
	$appID = $mybb->settings['myfbconnect_appid'];
	$appSecret = $mybb->settings['myfbconnect_appsecret'];
	
	// store some defaults
	$do_loginUrl = $mybb->settings['bburl'] . "/index.php?action=do_fblogin";
	
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
		
		// empty configuration
		if (empty($appID) OR empty($appSecret)) {
			error($lang->myfbconnect_error_noconfigfound);
		}
		
		// get the true login url
		$_loginUrl = $facebook->getLoginUrl(array(
			'scope' => 'user_birthday, user_location, email, publish_stream',
			'redirect_uri' => $do_loginUrl
		));
		
		// redirect to ask for permissions or to login if the user already granted them
		header("Location: " . $_loginUrl);
	}
	
	// don't stop the magic
	if ($mybb->input['action'] == "do_fblogin") {
		// get the user
		$user = $facebook->getUser();
		// guest detected!
		if (!$mybb->user['uid']) {
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
					$user = NULL;
				}
			}
			if (!$user) {
				error($lang->myfbconnect_error_noauth);
			}
		}
		// user detected, just tell him he his already logged in
		else {
			error($lang->myfbconnect_error_alreadyloggedin);
		}
	}
}

/**
 * Logins or registers a new user with Facebook, provided a valid ID.
 * 
 * @param array The user data containing all the information which are parsed and inserted into the database.
 * @return boolean True if successful, false if unsuccessful.
 **/

function myfbconnect_run($userdata)
{
	
	global $mybb, $db, $session, $lang;
	
	$user = $userdata;
	
	// See if this user is already present in our database
	$query = $db->simple_select("users", "*", "myfb_uid='{$user['id']}'");
	$facebookID = $db->fetch_array($query);
	
	// this user hasn't a linked-to-facebook account yet
	if (!$facebookID) {
		// link the Facebook ID to our user if found, searching for the same email
		$query = $db->simple_select("users", "*", "email='{$user['email']}'");
		$registered = $db->fetch_array($query);
		// this user is already registered with us, just link its account with his facebook and log him in
		if ($registered) {
			$db->query("UPDATE " . TABLE_PREFIX . "users SET myfb_uid = {$user['id']} WHERE email = '{$user['email']}'");
			$db->delete_query("sessions", "ip='" . $db->escape_string($session->ipaddress) . "' AND sid != '" . $session->sid . "'");
			$newsession = array(
				"uid" => $registered['uid']
			);
			$db->update_query("sessions", $newsession, "sid='" . $session->sid . "'");
			
			// let it sync
			myfbconnect_sync($registered, $user);
			
			my_setcookie("mybbuser", $registered['uid'] . "_" . $registered['loginkey'], null, true);
			my_setcookie("sid", $session->sid, -1, true);
			redirect("index.php", $lang->myfbconnect_redirect_loggedin, $lang->sprintf($lang->myfbconnect_redirect_title, $user['name']));
		}
		// this user isn't registered with us, so we have to register it
		else {
			require_once MYBB_ROOT . "inc/datahandlers/user.php";
			$userhandler = new UserDataHandler("insert");
			
			$password = random_str(8);
			
			$newUser = array(
				"username" => $user['name'],
				"password" => $password,
				"password2" => $password,
				"email" => $user['email'],
				"email2" => $user['email'],
				"usergroup" => $mybb->settings['myfbconnect_usergroup'],
				"regip" => $session->ipaddress,
				"longregip" => my_ip2long($session->ipaddress)
			);
			
			$userhandler->set_data($newUser);
			if ($userhandler->validate_user()) {
				$newUserData = $userhandler->insert_user();
				
	
				/*$appID = $mybb->settings['myfbconnect_appid'];
				$appSecret = $mybb->settings['myfbconnect_appsecret'];
				
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
				// we successfully registered, now post something on the wall of this user
				try {
					$facebook->api('/me/feed', 'POST',
                                    array(
                                      'link' => 'http://www.idevicelab.net/forum',
                                      'message' => 'Test finali per la registrazione e il login tramite Facebook: tra non molto su iDeviceLAB!'
                                 ));
				}
				// user found, but permissions denied
				catch (FacebookApiException $e) {
					$noauth = true;
				}
				if ($noauth) {
					error($lang->myfbconnect_error_noauth);
				}*/
			}
			// the username is already in use, let the user choose one from scratch
			else {
				$error = $userhandler->get_errors();
				error($lang->sprintf($lang->myfbconnect_error_usernametaken, $error['username_exists']['data']['0']));
			}
			
			
			myfbconnect_sync($newUserData, $user);
			
			// after registration we have to log this new user in
			my_setcookie("mybbuser", $newUserData['uid'] . "_" . $newUserData['loginkey'], null, true);
			redirect("index.php", $lang->myfbconnect_redirect_registered, $lang->sprintf($lang->myfbconnect_redirect_title, $user['name']));
		}
	}
	// this user has already a linked-to-facebook account, just log him in and update session
	else {
		$db->delete_query("sessions", "ip='" . $db->escape_string($session->ipaddress) . "' AND sid != '" . $session->sid . "'");
		$newsession = array(
			"uid" => $facebookID['uid']
		);
		$db->update_query("sessions", $newsession, "sid='" . $session->sid . "'");
		
		// eventually sync data
		myfbconnect_sync($facebookID, $user);
		
		my_setcookie("mybbuser", $facebookID['uid'] . "_" . $facebookID['loginkey'], null, true);
		my_setcookie("sid", $session->sid, -1, true);
		redirect("index.php", $lang->myfbconnect_redirect_loggedin, $lang->sprintf($lang->myfbconnect_redirect_title, $user['name']));
	}
	
}

/**
 * Syncronizes any Facebook account with any MyBB account, importing all the infos.
 * 
 * @param array The existing user data. UID is required.
 * @param array The Facebook user data to sync.
 * @param int Whether to sync regardless of elements presence or not. Disabled by default.
 * @return boolean True if successful, false if unsuccessful.
 **/

function myfbconnect_sync($user = array(), $fbdata = array(), $bypass = false)
{
	
	global $mybb, $db, $session, $lang, $plugins;
	
	$userData = array();
	
	// facebook id, if empty we need to sync it
	if (empty($user["myfb_uid"])) {
		$userData["myfb_uid"] = $fbdata["id"];
	}
	// ======                     begin our checkes comparing mybb with facebook stuff                   ======
	// ======  Syntax to use: !empty(FACEBOOK VALUE) AND (empty(EXISTING VALUE) OR $bypass) AND PATCHES  ======
	
	// avatar
	if (!empty($fbdata['id']) AND (empty($user['avatar']) OR $bypass)) {
		$userData["avatar"] = "http://graph.facebook.com/{$fbdata['id']}/picture?type=large";
		$userData["avatartype"] = "remote";

		// Copy the avatar to the local server (work around remote URL access disabled for getimagesize)
		$file = fetch_remote_file($userData["avatar"]);
		$tmp_name = $mybb->settings['avataruploadpath']."/remote_".md5(random_str());
		$fp = @fopen($tmp_name, "wb");
		if($fp)	{
			fwrite($fp, $file);
			fclose($fp);
			list($width, $height, $type) = @getimagesize($tmp_name);
			@unlink($tmp_name);
			if(!$type) {
				$avatar_error = true;
			}
		}
		
		list($maxwidth, $maxheight) = explode("x", my_strtolower($mybb->settings['maxavatardims']));

		if(empty($avatar_error)) {
			if($width && $height && $mybb->settings['maxavatardims'] != "") {
				if(($maxwidth && $width > $maxwidth) || ($maxheight && $height > $maxheight)) {
					$avatardims = $maxheight."|".$maxwidth;
				}
			}
			if($width > 0 && $height > 0 && !$avatardims)
			{
				$avatardims = $width."|".$height;
			}
			$userData["avatardimensions"] = $avatardims;
		}
		else {
			$userData["avatardimensions"] = $maxheight."|".$maxwidth;
		}
	}
	// birthday
	if (!empty($fbdata['birthday']) AND (empty($user['birthday']) OR $bypass)) {
		$birthday = explode("/", $fbdata['birthday']);
		$birthday['0'] = ltrim($birthday['0'], '0');
		$userData["birthday"] = $birthday['1']."-".$birthday['0']."-".$birthday['2'];
	}
	// cover, if Profile Picture plugin is installed
	if (!empty($fbdata['cover']['source']) AND $db->field_exists("profilepic", "users") AND (empty($user['profilepic']) OR $bypass)) {
		$cover = $fbdata['cover']['source'];
		$userData["profilepic"] = str_replace('/s720x720/', '/p851x315/', $cover);
		$userData["profilepictype"] = "remote";
		if ($mybb->usergroup['profilepicmaxdimensions']) {
			list($maxwidth, $maxheight) = explode("x", my_strtolower($mybb->usergroup['profilepicmaxdimensions']));
			$userData["profilepicdimensions"] = $maxwidth."|".$maxheight;
		}
	}
	
	$plugins->run_hooks("myfbconnect_sync_end", $userData);
	
	// let's do it!
	if (!empty($userData) AND !empty($user['uid'])) {
		$db->update_query("users", $userData, "uid = {$user['uid']}");
	}
	
	return true;
	
}

function myfbconnect_debug($data)
{
	echo "<pre>";
	echo var_dump($data);
	echo "</pre>";
	exit;
}