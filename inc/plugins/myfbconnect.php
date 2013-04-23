<?php
/**
 * MyFacebook Connect
 * 
 * Integrates MyBB with Facebook, featuring login and registration.
 *
 * @package MyFacebook Connect
 * @author  Shade <legend_k@live.it>
 * @license http://opensource.org/licenses/mit-license.php MIT license
 * @version 1.0.2
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
		'website' => 'https://github.com/Shade-/MyFacebook-Connect',
		'author' => 'Shade',
		'authorsite' => 'http://www.idevicelab.net/forum',
		'version' => '1.0.2',
		'compatibility' => '16*',
		'guid' => 'c5627aab08ec4d321e71afd2b9d02fb2'
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
		'fastregistration' => array(
			'title' => $lang->myfbconnect_settings_fastregistration,
			'description' => $lang->myfbconnect_settings_fastregistration_desc,
			'value' => '1'
		),
		'usergroup' => array(
			'title' => $lang->myfbconnect_settings_usergroup,
			'description' => $lang->myfbconnect_settings_usergroup_desc,
			'value' => '2',
			'optionscode' => 'text'
		),
		'requestpublishingperms' => array(
			'title' => $lang->myfbconnect_settings_requestpublishingperms,
			'description' => $lang->myfbconnect_settings_requestpublishingperms_desc,
			'value' => '0'
		),
		'passwordpm' => array(
			'title' => $lang->myfbconnect_settings_passwordpm,
			'description' => $lang->myfbconnect_settings_passwordpm_desc,
			'value' => '1'
		),
		'passwordpm_subject' => array(
			'title' => $lang->myfbconnect_settings_passwordpm_subject,
			'description' => $lang->myfbconnect_settings_passwordpm_subject_desc,
			'optionscode' => 'text',
			'value' => $lang->myfbconnect_default_passwordpm_subject
		),
		'passwordpm_message' => array(
			'title' => $lang->myfbconnect_settings_passwordpm_message,
			'description' => $lang->myfbconnect_settings_passwordpm_message_desc,
			'optionscode' => 'textarea',
			'value' => $lang->myfbconnect_default_passwordpm_message
		),
		'passwordpm_fromid' => array(
			'title' => $lang->myfbconnect_settings_passwordpm_fromid,
			'description' => $lang->myfbconnect_settings_passwordpm_fromid_desc,
			'optionscode' => 'text',
			'value' => ''
		),
		// birthday
		'fbbday' => array(
			'title' => $lang->myfbconnect_settings_fbbday,
			'description' => $lang->myfbconnect_settings_fbbday_desc,
			'value' => '1'
		),
		// location
		'fblocation' => array(
			'title' => $lang->myfbconnect_settings_fblocation,
			'description' => $lang->myfbconnect_settings_fblocation_desc,
			'value' => '1'
		),
		'fblocationfield' => array(
			'title' => $lang->myfbconnect_settings_fblocationfield,
			'description' => $lang->myfbconnect_settings_fblocationfield_desc,
			'optionscode' => 'text',
			'value' => '1'
		),
		// bio
		'fbbio' => array(
			'title' => $lang->myfbconnect_settings_fbbio,
			'description' => $lang->myfbconnect_settings_fbbio_desc,
			'value' => '1'
		),
		'fbbiofield' => array(
			'title' => $lang->myfbconnect_settings_fbbiofield,
			'description' => $lang->myfbconnect_settings_fbbiofield_desc,
			'optionscode' => 'text',
			'value' => '2'
		),
		// name and last name
		'fbdetails' => array(
			'title' => $lang->myfbconnect_settings_fbdetails,
			'description' => $lang->myfbconnect_settings_fbdetails_desc,
			'value' => '0'
		),
		'fbdetailsfield' => array(
			'title' => $lang->myfbconnect_settings_fbdetailsfield,
			'description' => $lang->myfbconnect_settings_fbdetailsfield_desc,
			'optionscode' => 'text',
			'value' => ''
		),
		// sex - does nothing atm!
		'fbsex' => array(
			'title' => $lang->myfbconnect_settings_fbsex,
			'description' => $lang->myfbconnect_settings_fbsex_desc,
			'value' => '0'
		),
		'fbsexfield' => array(
			'title' => $lang->myfbconnect_settings_fbsexfield,
			'description' => $lang->myfbconnect_settings_fbsexfield_desc,
			'optionscode' => 'text',
			'value' => '3'
		)
	));
	
	// insert our Facebook columns into the database
	$db->query("ALTER TABLE " . TABLE_PREFIX . "users ADD (
		`fbavatar` int(1) NOT NULL DEFAULT 1,
		`fbsex` int(1) NOT NULL DEFAULT 1,
		`fbdetails` int(1) NOT NULL DEFAULT 1,
		`fbbio` int(1) NOT NULL DEFAULT 1,
		`fbbday` int(1) NOT NULL DEFAULT 1,
		`fblocation` int(1) NOT NULL DEFAULT 1,
		`myfb_uid` bigint(50) NOT NULL DEFAULT 0
		)");
	
	// Euantor's templating system	   
	$dir = new DirectoryIterator(dirname(__FILE__) . '/MyFacebookConnect/templates');
	$templates = array();
	foreach ($dir as $file) {
		if (!$file->isDot() AND !$file->isDir() AND pathinfo($file->getFilename(), PATHINFO_EXTENSION) == 'html') {
			$templates[$file->getBasename('.html')] = file_get_contents($file->getPathName());
		}
	}
	
	$PL->templates('myfbconnect', 'MyFacebook Connect', $templates);
	
	// create cache
	$info = myfbconnect_info();
	$shadePlugins = $cache->read('shade_plugins');
	$shadePlugins[$info['name']] = array(
		'title' => $info['name'],
		'version' => $info['version']
	);
	$cache->update('shade_plugins', $shadePlugins);
	
	require_once MYBB_ROOT . 'inc/adminfunctions_templates.php';
	
	find_replace_templatesets('header_welcomeblock_guest', '#' . preg_quote('{$lang->welcome_register}</a>') . '#i', '{$lang->welcome_register}</a> &mdash; <a href="{$mybb->settings[\'bburl\']}/myfbconnect.php?action=fblogin">{$lang->myfbconnect_login}</a>');
	
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
	
	// delete our Facebook columns
	$db->query("ALTER TABLE " . TABLE_PREFIX . "users DROP `fbavatar`, DROP `fbsex`, DROP `fbdetails`, DROP `fbbio`, DROP `fbbday`, DROP `fblocation`, DROP `myfb_uid`");
	
	$info = myfbconnect_info();
	// delete the plugin from cache
	$shadePlugins = $cache->read('shade_plugins');
	unset($shadePlugins[$info['name']]);
	$cache->update('shade_plugins', $shadePlugins);
	
	$PL->templates_delete('myfbconnect');
	
	require_once MYBB_ROOT . 'inc/adminfunctions_templates.php';
	
	find_replace_templatesets('header_welcomeblock_guest', '#' . preg_quote('&mdash; <a href="{$mybb->settings[\'bburl\']}/myfbconnect.php?action=fblogin">{$lang->myfbconnect_login}</a>') . '#i', '');
	
	// rebuild settings
	rebuild_settings();
}

global $mybb, $settings;

if ($settings['myfbconnect_enabled']) {
	$plugins->add_hook('global_start', 'myfbconnect_global');
	$plugins->add_hook('usercp_menu', 'myfbconnect_usercp_menu', 40);
	$plugins->add_hook('usercp_start', 'myfbconnect_usercp');
	$plugins->add_hook("admin_page_output_footer", "myfbconnect_settings_footer");
}

function myfbconnect_global()
{
	
	global $mybb, $lang, $templatelist;
	
	if (!$lang->myfbconnect) {
		$lang->load("myfbconnect");
	}
	
	if (isset($templatelist)) {
		$templatelist .= ',';
	}
	
	if (THIS_SCRIPT == "myfbconnect.php") {
		$templatelist .= 'myfbconnect_register';
	}
	
	if (THIS_SCRIPT == "usercp.php") {
		$templatelist .= 'myfbconnect_usercp_menu';
	}
	
	if (THIS_SCRIPT == "usercp.php" AND $mybb->input['action'] == "myfbconnect") {
		$templatelist .= ',myfbconnect_usercp_settings,myfbconnect_usercp_settings_linkprofile,myfbconnect_usercp_showsettings,myfbconnect_usercp_settings_setting';
	}
}

function myfbconnect_usercp_menu()
{
	
	global $mybb, $templates, $theme, $usercpmenu, $lang, $collapsed, $collapsedimg;
	
	if (!$lang->myfbconnect) {
		$lang->load("myfbconnect");
	}
	
	eval("\$usercpmenu .= \"" . $templates->get('myfbconnect_usercp_menu') . "\";");
}

function myfbconnect_usercp()
{
	
	global $mybb, $lang;
	
	if (!$lang->myfbconnect) {
		$lang->load('myfbconnect');
	}
	
	if ($mybb->input['action'] == ("do_fblink" OR "myfbconnect") OR ($mybb->input['action'] == ("do_fblink" OR "myfbconnect") AND $mybb->request_method == 'post')) {
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
	}
	
	// linking accounts
	if ($mybb->input['action'] == "fblink") {
		$loginUrl = "/usercp.php?action=do_fblink";
		myfbconnect_login($loginUrl);
	}
	
	// truly link accounts
	if ($mybb->input['action'] == "do_fblink") {
		// get the user
		$user = $facebook->getUser();
		if ($user) {
			$userdata['id'] = $user;
			// true means only link
			myfbconnect_run($userdata, true);
			// inline success support
			if (function_exists(inline_success)) {
				$inlinesuccess = inline_success($lang->myfbconnect_success_linked);
				$mybb->input['action'] = "myfbconnect";
				// make sure we don't update options when redirecting with inline success (with NULL values)
				unset($mybb->input['code']);
			} else {
				redirect("usercp.php?action=myfbconnect", $lang->myfbconnect_success_linked);
			}
		} else {
			error($lang->myfbconnect_error_noauth);
		}
	}
	
	// settings page
	if ($mybb->input['action'] == 'myfbconnect') {
		global $db, $lang, $theme, $templates, $headerinclude, $header, $footer, $plugins, $usercpnav;
		
		add_breadcrumb($lang->nav_usercp, 'usercp.php');
		add_breadcrumb($lang->myfbconnect_page_title, 'usercp.php?action=myfbconnect');
		
		// 2 situations provided: the user is logged in with Facebook, two user isn't logged in with Facebook but it's loggin in.
		if ($mybb->request_method == 'post' OR $_SESSION['fb_isloggingin']) {
			
			session_start();
			
			if ($mybb->request_method == 'post') {
				verify_post_check($mybb->input['my_post_key']);
			}
			
			$settings = array();
			$settingsToCheck = array(
				"fbavatar",
				"fbsex",
				"fbdetails",
				"fbbio",
				"fbbday",
				"fblocation"
			);
			
			// having some fun with variable variables
			foreach ($settingsToCheck as $setting) {
				if ($mybb->input[$setting] == 1) {
					$settings[$setting] = 1;
				} else {
					$settings[$setting] = 0;
				}
				// building the extra data passed to the redirect url of the login function
				$loginUrlExtra .= "&{$setting}=" . $settings[$setting];
			}
			
			if (!$facebook->getUser()) {
				$loginUrl = "/usercp.php?action=myfbconnect" . $loginUrlExtra;
				// used for recognizing an active settings update process later on
				$_SESSION['fb_isloggingin'] = true;
				myfbconnect_login($loginUrl);
			}
			
			if ($db->update_query('users', $settings, 'uid = ' . (int) $mybb->user['uid'])) {
				// update on-the-fly that array of data dude!
				$newUser = array_merge($mybb->user, $settings);
				// oh yeah, let's sync!
				myfbconnect_sync($newUser);
				
				// we don't need fb_isloggingin anymore
				unset($_SESSION['fb_isloggingin']);
				// inline success support
				if (function_exists(inline_success)) {
					$inlinesuccess = inline_success($lang->myfbconnect_success_settingsupdated);
				} else {
					redirect('usercp.php?action=myfbconnect', $lang->myfbconnect_success_settingsupdated, $lang->myfbconnect_success_settingsupdated_title);
				}
			}
		}
		
		$query = $db->simple_select("users", "myfb_uid", "uid = " . $mybb->user['uid']);
		$alreadyThere = $db->fetch_field($query, "myfb_uid");
		$options = "";
		
		if ($alreadyThere) {
			
			$text = $lang->myfbconnect_settings_whattosync;
			// checking if we want to sync that stuff
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
					$settingsToSelect[] = $setting;
				}
			}
			
			// join pieces into a string
			if (!empty($settingsToSelect)) {
				$settingsToSelect = "," . implode(",", $settingsToSelect);
			}
			
			$query = $db->simple_select("users", "fbavatar" . $settingsToSelect, "uid = " . $mybb->user['uid']);
			$userSettings = $db->fetch_array($query);
			$settings = "";
			foreach ($userSettings as $setting => $value) {
				// variable variables. Yay!
				$tempKey = 'myfbconnect_settings_' . $setting;
				if ($value == 1) {
					$checked = " checked=\"checked\"";
				} else {
					$checked = "";
				}
				$label = $lang->$tempKey;
				$altbg = alt_trow();
				eval("\$options .= \"" . $templates->get('myfbconnect_usercp_settings_setting') . "\";");
			}
		} else {
			$text = $lang->myfbconnect_settings_linkaccount;
			eval("\$options = \"" . $templates->get('myfbconnect_usercp_settings_linkprofile') . "\";");
		}
		
		eval("\$content = \"" . $templates->get('myfbconnect_usercp_settings') . "\";");
		output_page($content);
	}
}

/**
 * Main function which logins or registers any kind of Facebook user, provided a valid ID.
 * 
 * @param array The user data containing all the information which are parsed and inserted into the database.
 * @param boolean (optional) Whether to simply link the profile to FB or not. Default to false.
 * @return boolean True if successful, false if unsuccessful.
 **/

function myfbconnect_run($userdata, $justlink = false)
{
	
	global $mybb, $db, $session, $lang;
	
	$user = $userdata;
	
	// See if this user is already present in our database
	if (!$justlink) {
		$query = $db->simple_select("users", "*", "myfb_uid = {$user['id']}");
		$facebookID = $db->fetch_array($query);
	}
	
	// this user hasn't a linked-to-facebook account yet
	if (!$facebookID OR $justlink) {
		// link the Facebook ID to our user if found, searching for the same email
		if ($user['email']) {
			$query = $db->simple_select("users", "*", "email='{$user['email']}'");
			$registered = $db->fetch_array($query);
		}
		// this user is already registered with us, just link its account with his facebook and log him in
		if ($registered OR $justlink) {
			if ($justlink) {
				$db->update_query("users", array(
					"myfb_uid" => $user['id']
				), "uid = {$mybb->user['uid']}");
				return;
			}
			$db->update_query("users", array(
				"myfb_uid" => $user['id']
			), "email = '{$user['email']}'");
			$db->delete_query("sessions", "ip='" . $db->escape_string($session->ipaddress) . "' AND sid != '" . $session->sid . "'");
			$newsession = array(
				"uid" => $registered['uid']
			);
			$db->update_query("sessions", $newsession, "sid='" . $session->sid . "'");
			
			// let it sync, let it sync
			myfbconnect_sync($registered, $user);
			
			my_setcookie("mybbuser", $registered['uid'] . "_" . $registered['loginkey'], null, true);
			my_setcookie("sid", $session->sid, -1, true);
			
			// redirect
			if ($_SERVER['HTTP_REFERER'] AND strpos($_SERVER['HTTP_REFERER'], "action=fblogin") === false) {
				$redirect_url = htmlentities($_SERVER['HTTP_REFERER']);
			} else {
				$redirect_url = "index.php";
			}
			redirect($redirect_url, $lang->myfbconnect_redirect_loggedin, $lang->sprintf($lang->myfbconnect_redirect_title, $registered['username']));
		}
		// this user isn't registered with us, so we have to register it
		else {
			
			// if we want to let the user choose some infos, then pass the ball to our custom page			
			if (!$mybb->settings['myfbconnect_fastregistration']) {
				header("Location: myfbconnect.php?action=fbregister");
				return;
			}
			
			$newUserData = myfbconnect_register($user);
			if ($newUserData['error']) {
				return $newUserData;
			} else {
				// enable all options and sync
				$newUserDataSettings = array(
					"fbavatar" => 1,
					"fbbday" => 1,
					"fbsex" => 1,
					"fbdetails" => 1,
					"fbbio" => 1,
					"fblocation" => 1
				);
				$newUserData = array_merge($newUserData, $newUserDataSettings);
				myfbconnect_sync($newUserData, $user);
				// after registration we have to log this new user in
				my_setcookie("mybbuser", $newUserData['uid'] . "_" . $newUserData['loginkey'], null, true);
				
				if ($_SERVER['HTTP_REFERER'] AND strpos($_SERVER['HTTP_REFERER'], "action=fblogin") === false AND strpos($_SERVER['HTTP_REFERER'], "action=do_fblogin") === false) {
					$redirect_url = htmlentities($_SERVER['HTTP_REFERER']);
				} else {
					$redirect_url = "index.php";
				}
				
				redirect($redirect_url, $lang->myfbconnect_redirect_registered, $lang->sprintf($lang->myfbconnect_redirect_title, $user['name']));
			}
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
		
		// finally log the user in
		my_setcookie("mybbuser", $facebookID['uid'] . "_" . $facebookID['loginkey'], null, true);
		my_setcookie("sid", $session->sid, -1, true);
		// redirect the user to where he came from
		if ($_SERVER['HTTP_REFERER'] AND strpos($_SERVER['HTTP_REFERER'], "action=fblogin") === false) {
			$redirect_url = htmlentities($_SERVER['HTTP_REFERER']);
		} else {
			$redirect_url = "index.php";
		}
		redirect($redirect_url, $lang->myfbconnect_redirect_loggedin, $lang->sprintf($lang->myfbconnect_redirect_title, $facebookID['username']));
	}
	
}

/**
 * Unlink any Facebook account from the corresponding MyBB account.
 * 
 * @param int The UID of the user you want to unlink.
 * @return boolean True if successful, false if unsuccessful.
 **/

function myfbconnect_unlink($uid)
{
	
	global $db;
	
	$uid = (int) $uid;
	
	$reset = array(
		"myfb_uid" => 0
	);
	
	$db->update_query("users", $reset, "uid = {$uid}");
	
}

/**
 * Registers an user, provided an array with valid data.
 * 
 * @param array The data of the user to register. name and email keys must be present.
 * @return boolean True if successful, false if unsuccessful.
 **/

function myfbconnect_register($user = array())
{
	
	global $mybb, $session, $plugins, $lang;
	
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
		"displaygroup" => $mybb->settings['myfbconnect_usergroup'],
		"regip" => $session->ipaddress,
		"longregip" => my_ip2long($session->ipaddress)
	);
		
	/* Registration might fail for custom profile fields required at registration... workaround = IN_ADMINCP defined.
	 Placed straight before the registration process to avoid conflicts with third party plugins messying around with
	 templates (I'm looking at you, PHPTPL) */
	define("IN_ADMINCP", 1);
	
	$userhandler->set_data($newUser);
	if ($userhandler->validate_user()) {
		$newUserData = $userhandler->insert_user();
		
		if ($mybb->settings['myfbconnect_passwordpm']) {
			require_once MYBB_ROOT . "inc/datahandlers/pm.php";
			$pmhandler = new PMDataHandler();
			$pmhandler->admin_override = true;
			
			// just make sure the admins didn't make something wrong in configuration
			if (empty($mybb->settings['myfbconnect_passwordpm_fromid']) OR !user_exists($mybb->settings['myfbconnect_passwordpm_fromid'])) {
				$fromid = 0;
			} else {
				$fromid = (int) $mybb->settings['myfbconnect_passwordpm_fromid'];
			}
			
			$message = $mybb->settings['myfbconnect_passwordpm_message'];
			$subject = $mybb->settings['myfbconnect_passwordpm_subject'];
			
			$thingsToReplace = array(
				"{user}" => $newUserData['username'],
				"{password}" => $password
			);
			
			// replace what needs to be replaced
			foreach ($thingsToReplace as $find => $replace) {
				$message = str_replace($find, $replace, $message);
			}
			
			$pm = array(
				"subject" => $subject,
				"message" => $message,
				"fromid" => $fromid,
				"toid" => array(
					$newUserData['uid']
				)
			);
			
			// some defaults :)
			$pm['options'] = array(
				"signature" => 1,
				"disablesmilies" => 0,
				"savecopy" => 0,
				"readreceipt" => 0
			);
			
			$pmhandler->set_data($pm);
			
			// Now let the pm handler do all the hard work
			if ($pmhandler->validate_pm()) {
				$pmhandler->insert_pm();
			} else {
				error($lang->sprintf($lang->myfbconnect_error_report, $pmhandler->get_errors()));
			}
		}
		// return our newly registered user data
		return $newUserData;
	} else {
		$errors['error'] = true;
		$errors['data'] = $userhandler->get_friendly_errors();
		return $errors;
	}
}

/**
 * Syncronizes any Facebook account with any MyBB account, importing all the infos.
 * 
 * @param array The existing user data. UID is required.
 * @param array The Facebook user data to sync.
 * @param int Whether to bypass any existing user settings or not. Disabled by default.
 * @return boolean True if successful, false if unsuccessful.
 **/

function myfbconnect_sync($user, $fbdata = array(), $bypass = false)
{
	
	global $mybb, $db, $session, $lang, $plugins;
	
	$userData = array();
	$userfieldsData = array();
	
	$detailsid = "fid" . $mybb->settings['myfbconnect_fbdetailsfield'];
	$locationid = "fid" . $mybb->settings['myfbconnect_fblocationfield'];
	$bioid = "fid" . $mybb->settings['myfbconnect_fbbiofield'];
	$sexid = "fid" . $mybb->settings['myfbconnect_fbsexfield'];
	
	// ouch! empty facebook data, we need to help this poor guy!
	if (empty($fbdata)) {
		
		$appID = $mybb->settings['myfbconnect_appid'];
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
		
		$fbuser = $facebook->getUser();
		if (!$fbuser) {
			error($lang->myfbconnect_error_unknown);
		} else {
			$fbdata = $facebook->api("/me?fields=id,name,email,cover,birthday,website,gender,bio,location");
		}
	}
	
	$query = $db->simple_select("userfields", "*", "ufid = {$user['uid']}");
	$userfields = $db->fetch_array($query);
	if (empty($userfields)) {
		$userfieldsData['ufid'] = $user['uid'];
	}
	
	// facebook id, if empty we need to sync it
	if (empty($user["myfb_uid"])) {
		$userData["myfb_uid"] = $fbdata["id"];
	}
	
	// begin our checkes comparing mybb with facebook stuff, syntax:
	// (USER SETTINGS AND !empty(FACEBOOK VALUE)) OR $bypass (eventually ADMIN SETTINGS)
	
	// avatar
	if (($user['fbavatar'] AND !empty($fbdata['id'])) OR $bypass) {
		$userData["avatar"] = $db->escape_string("http://graph.facebook.com/{$fbdata['id']}/picture?type=large");
		$userData["avatartype"] = "remote";
		
		// Copy the avatar to the local server (work around remote URL access disabled for getimagesize)
		$file = fetch_remote_file($userData["avatar"]);
		$tmp_name = $mybb->settings['avataruploadpath'] . "/remote_" . md5(random_str());
		$fp = @fopen($tmp_name, "wb");
		if ($fp) {
			fwrite($fp, $file);
			fclose($fp);
			list($width, $height, $type) = @getimagesize($tmp_name);
			@unlink($tmp_name);
			if (!$type) {
				$avatar_error = true;
			}
		}
		
		list($maxwidth, $maxheight) = explode("x", my_strtolower($mybb->settings['maxavatardims']));
		
		if (empty($avatar_error)) {
			if ($width AND $height AND $mybb->settings['maxavatardims'] != "") {
				if (($maxwidth AND $width > $maxwidth) OR ($maxheight AND $height > $maxheight)) {
					$avatardims = $maxheight . "|" . $maxwidth;
				}
			}
			if ($width > 0 AND $height > 0 AND !$avatardims) {
				$avatardims = $width . "|" . $height;
			}
			$userData["avatardimensions"] = $avatardims;
		} else {
			$userData["avatardimensions"] = $maxheight . "|" . $maxwidth;
		}
	}
	// birthday
	if ((($user['fbbday'] AND !empty($fbdata['birthday'])) OR $bypass) AND $mybb->settings['myfbconnect_fbbday']) {
		$birthday = explode("/", $fbdata['birthday']);
		$birthday['0'] = ltrim($birthday['0'], '0');
		$userData["birthday"] = $birthday['1'] . "-" . $birthday['0'] . "-" . $birthday['2'];
	}
	// cover, if Profile Picture plugin is installed
	if ((($user['fbavatar'] AND !empty($fbdata['cover']['source'])) OR $bypass) AND $db->field_exists("profilepic", "users")) {
		$cover = $fbdata['cover']['source'];
		$userData["profilepic"] = str_replace('/s720x720/', '/p851x315/', $cover);
		$userData["profilepictype"] = "remote";
		if ($mybb->usergroup['profilepicmaxdimensions']) {
			list($maxwidth, $maxheight) = explode("x", my_strtolower($mybb->usergroup['profilepicmaxdimensions']));
			$userData["profilepicdimensions"] = $maxwidth . "|" . $maxheight;
		} else {
			$userData["profilepicdimensions"] = "851|315";
		}
	}
	
	// sex
	if ((($user['fbsex'] AND !empty($fbdata['gender'])) OR $bypass) AND $mybb->settings['myfbconnect_fbsex']) {
		if ($db->field_exists($sexid, "userfields")) {
			if ($fbdata['gender'] == "male") {
				// italian fillings... 5h17! workaround needed!
				$userfieldsData[$sexid] = "Uomo";
			} elseif ($fbdata['gender'] == "female") {
				$userfieldsData[$sexid] = "Donna";
			}
		}
	}
	// name and last name
	if ((($user['fbdetails'] AND !empty($fbdata['name'])) OR $bypass) AND $mybb->settings['myfbconnect_fbdetails']) {
		if ($db->field_exists($detailsid, "userfields")) {
			$userfieldsData[$detailsid] = $fbdata['name'];
		}
	}
	// bio
	if ((($user['fbbio'] AND !empty($fbdata['bio'])) OR $bypass) AND $mybb->settings['myfbconnect_fbbio']) {
		if ($db->field_exists($bioid, "userfields")) {
			$userfieldsData[$bioid] = htmlspecialchars_decode(my_substr($fbdata['bio'], 0, 400, true));
		}
	}
	// location
	if ((($user['fblocation'] AND !empty($fbdata['location']['name'])) OR $bypass) AND $mybb->settings['myfbconnect_fblocation']) {
		if ($db->field_exists($locationid, "userfields")) {
			$userfieldsData[$locationid] = $fbdata['location']['name'];
		}
	}
	
	$plugins->run_hooks("myfbconnect_sync_end", $userData);
	
	// let's do it!
	if (!empty($userData) AND !empty($user['uid'])) {
		$db->update_query("users", $userData, "uid = {$user['uid']}");
	}
	// make sure we can do it
	if (!empty($userfieldsData) AND !empty($user['uid'])) {
		if (isset($userfieldsData['ufid'])) {
			$db->insert_query("userfields", $userfieldsData);
		} else {
			$db->update_query("userfields", $userfieldsData, "ufid = {$user['uid']}");
		}
	}
	
	return true;
	
}

/**
 * Logins any Facebook user, prompting a permission page and redirecting to the URL they came from.
 * 
 * @param mixed The URL to redirect at the end of the process. Relative URL.
 * @return redirect Redirects with an header() call to the specified URL.
 **/

function myfbconnect_login($url)
{
	global $mybb, $lang;
	
	$appID = $mybb->settings['myfbconnect_appid'];
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
	
	// if we have got an active access token it might be possible to login directly, without passing through Facebook
	try {
		$user = $facebook->api("/me");
		header("Location: ".$mybb->settings['bburl'].$url);
		return;
	}
	catch (FacebookApiException $e) {
		$noauth = true;
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
		'scope' => 'user_birthday, user_location, email' . $extraPermissions,
		'redirect_uri' => $mybb->settings['bburl'] . $url
	));
	
	// redirect to ask for permissions or to login if the user already granted them
	header("Location: " . $_loginUrl);
	return;
}

/**
 * Displays peekers in settings. Technique ripped from MySupport, please don't blame on me :(
 * 
 * @return boolean True if successful, false either.
 **/

function myfbconnect_settings_footer()
{
	global $mybb, $db;
	if ($mybb->input["action"] == "change" && $mybb->request_method != "post") {
		$gid = myfbconnect_settings_gid();
		if ($mybb->input["gid"] == $gid || !$mybb->input['gid']) {
			echo '<script type="text/javascript">
	Event.observe(window, "load", function() {
	loadMyFBConnectPeekers();
});
function loadMyFBConnectPeekers()
{
	new Peeker($$(".setting_myfbconnect_passwordpm"), $("row_setting_myfbconnect_passwordpm_subject"), /1/, true);
	new Peeker($$(".setting_myfbconnect_passwordpm"), $("row_setting_myfbconnect_passwordpm_message"), /1/, true);
	new Peeker($$(".setting_myfbconnect_passwordpm"), $("row_setting_myfbconnect_passwordpm_fromid"), /1/, true);
	new Peeker($$(".setting_myfbconnect_fbbio"), $("row_setting_myfbconnect_fbbiofield"), /1/, true);
	new Peeker($$(".setting_myfbconnect_fblocation"), $("row_setting_myfbconnect_fblocationfield"), /1/, true);
	new Peeker($$(".setting_myfbconnect_fbdetails"), $("row_setting_myfbconnect_fbdetailsfield"), /1/, true);
	new Peeker($$(".setting_myfbconnect_fbsex"), $("row_setting_myfbconnect_fbsexfield"), /1/, true);
}
</script>';
		}
	}
}

/**
 * Gets the gid of MyFacebook Connect settings group.
 * 
 * @return mixed The gid.
 **/

function myfbconnect_settings_gid()
{
	global $db;
	
	$query = $db->simple_select("settinggroups", "gid", "name = 'myfbconnect'", array(
		"limit" => 1
	));
	$gid = $db->fetch_field($query, "gid");
	
	return intval($gid);
}

/**
 * Debugs any type of data.
 * 
 * @param mixed The data to debug.
 * @return mixed The debugged data.
 **/

function myfbconnect_debug($data)
{
	echo "<pre>";
	echo var_dump($data);
	echo "</pre>";
	exit;
}

/*****************************************************************************************
 *
 * The following is a function used to upgrade from older versions of the plugin
 *
 *****************************************************************************************/

if ($mybb->settings['myfbconnect_enabled']) {
	$plugins->add_hook("admin_page_output_header", "myfbconnect_upgrader");
}

function myfbconnect_upgrader()
{
	
	global $db, $mybb, $cache, $lang;
	
	if (!$lang->myfbconnect) {
		$lang->load("myfbconnect");
	}
	
	// let's see what version of MyFacebook Connect is currently installed on this board
	$info = myfbconnect_info();
	$shadePlugins = $cache->read('shade_plugins');
	$oldversion = $shadePlugins[$info['name']]['version'];
	$currentversion = $info['version'];
	
	// you need to update buddy!
	if (version_compare($oldversion, $currentversion, "<")) {
		flash_message($lang->myfbconnect_error_needtoupdate, "error");
	}
	
	// you are updating, that's nice!
	if ($mybb->input['upgrade'] == "myfbconnect") {
		// let's check if you should upgrade first
		if (version_compare($oldversion, $currentversion, "<")) {
			// yeah you should
			// 1.0
			if (version_compare($oldversion, "1.0", "<=")) {
				require_once MYBB_ROOT . "inc/adminfunctions_templates.php";
				find_replace_templatesets('myfbconnect_register', '#' . preg_quote('<td valign="top">') . '#i', '<td valign="top">{$errors}');
			}
			// update version n° and return a success message
			$shadePlugins[$info['name']] = array(
				'title' => $info['name'],
				'version' => $currentversion
			);
			$cache->update('shade_plugins', $shadePlugins);
			flash_message($lang->sprintf($lang->myfbconnect_success_updated, $oldversion, $currentversion), "success");
			admin_redirect($_SERVER['HTTP_REFERER']);
		} else {
			// you shouldn't
			flash_message($lang->myfbconnect_error_nothingtodohere, "error");
			admin_redirect("index.php");
		}
	}
}