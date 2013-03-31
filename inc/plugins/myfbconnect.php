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
		'name'			=>	'MyFacebook Connect',
		'description'	=>	'Integrates MyBB with Facebook, featuring login and registration.',
		'website'		=>	'https://github.com/Shade-/MyFacebookConnect',
		'author'		=>	'Shade',
		'authorsite'	=>	'http://www.idevicelab.net/forum',
		'version'		=>	'alpha 1',
		'compatibility'	=>	'16*',
		'guid'			=>	'none... yet'
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
	$db->query("ALTER TABLE ".TABLE_PREFIX."users ADD `myfb_uid` bigint(50) NOT NULL");
	
	// create cache
	$info = myfbconnect_info();
	$shadePlugins = $cache->read('shade_plugins');
	$shadePlugins[$info['name']] = array(
		'title'		=>	$info['name'],
		'version'	=>	$info['version']
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
	$db->query("ALTER TABLE ".TABLE_PREFIX."users drop `myfb_uid`");
	
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

function myfbconnect_pre_output(&$contents) {
	
	global $mybb;
	
	$loginUrl = $mybb->settings['bburl']."/index.php?action=fblogin";
	$loginUrl = "<a href=\"{$loginUrl}\">Facebook Login</a>";
	
	$contents = str_replace('<fblogintest>', $loginUrl, $contents);

    return $contents;
}

function myfbconnect_index_end() {
	
	global $mybb, $lang;
	
	if (!$lang->myfbconnect) {
		$lang->load('myfbconnect');
	}
	
	$appID = $mybb->settings['myfbconnect_appid'];
	$appSecret = $mybb->settings['myfbconnect_appsecret'];
	
	// store some defaults
	$do_loginUrl = $mybb->settings['bburl']."/index.php?action=do_fblogin";
	$redirectUrl = $_SERVER['HTTP_REFERER'];
	
	$loginUrl = "<a href=\"{$loginUrl}\">Facebook Login</a>";
	
	// include our API
	try{
		include_once MYBB_ROOT."myfbconnect/src/facebook.php";
	} catch(Exception $e) {
		error_log($e);
	}
	
	// Create our application instance
	$facebook = new Facebook(array(
		'appId'		=> $appID,
		'secret'	=> $appSecret,
	));
	
	// start all the magic
	if($mybb->input['action'] == "fblogin") {
		
		// empty configuration
		if(empty($appID) OR empty($appSecret)) {
			error($lang->myfbconnect_error_noconfigfound);
		}
		
		// get the true login url
		$_loginUrl = $facebook->getLoginUrl(array(
			'scope' => 'user_birthday, user_location, email',
			'redirect_uri' => $do_loginUrl
		));
		
		// redirect to ask for permissions or to login if the user already granted them
		header("Location: ".$_loginUrl);
	}
	
	// don't stop the magic
	if($mybb->input['action'] == "do_fblogin") {
		// get the user
		$user = $facebook->getUser();
		// guest detected!
		if(!$mybb->user['uid']) {
			if($user) {
				// user found and logged in
				try {
					// get the user public data
					$userdata = $facebook->api("/me");
					// let our handler do all the hard work
					myfbconnect_run($userdata);
					header("Location: ".$redirectUrl);
				}
				// user found, but permissions denied
				catch(FacebookApiException $e) {
					$user = NULL;
				}
			}
			if(!$user) {
				error($lang->myfbconnect_error_noauth);
			}
		}
		// user detected!
		else {
		}
	}
}

function myfbconnect_run($userdata) {
	
	global $mybb, $db, $session, $lang;
	
	$user = $userdata;
	
	// See if this user is already present in our database
	$query = $db->simple_select("users", "*", "myfb_uid='{$user['id']}'");
	$facebookID = $db->fetch_array($query);
	
	// this user hasn't a linked-to-facebook account yet
	if(!$facebookID) {
		// link the Facebook ID to our user if found, searching for the same email
		$query = $db->simple_select("users", "*", "email='{$user['email']}'");
		$registered = $db->fetch_array($query);
		// this user is already registered with us, just link its account with his facebook and log him in
		if($registered) {
			$db->query("UPDATE ".TABLE_PREFIX."users SET myfb_uid = {$user['id']} WHERE email = '{$user['email']}'");
			$db->delete_query("sessions", "ip='".$db->escape_string($session->ipaddress)."' AND sid != '".$session->sid."'");
			$newsession = array(
				"uid" => $registered['uid'],
			);
			$db->update_query("sessions", $newsession, "sid='".$session->sid."'");
			
			$syncData = array(
				"myfb_uid" => $user['id']
				);
			
			if(!$registered['avatar']) {
				list($maxwidth, $maxheight) = explode("x", my_strtolower($mybb->settings['maxavatardims']));
				$syncData["avatar"] = "http://graph.facebook.com/{$user['id']}/picture?type=large";
				$syncData["avatardimensions"] = $maxwidth."|".$maxheight;
				$syncData["avatartype"] = "remote";
			}
			
			$db->update_query("users", $syncData, "email = '{$user['email']}'");
			
			my_setcookie("mybbuser", $registered['uid']."_".$registered['loginkey'], null, true);
			my_setcookie("sid", $session->sid, -1, true);
		}
		// this user isn't registered with us, so we have to register it
		else {
			require_once  MYBB_ROOT."inc/datahandlers/user.php";
			$userhandler = new UserDataHandler("insert");
			
			$password = random_str(8);
			
			$newUser = array(
				"username" => $user['name'],
				"password" => $password,
				"password2" => $password,
				"email" => $user['email'],
				"email2" => $user['email'],
				"usergroup" => $mybb->settings['myfbconnect_usergroup'],
				"avatar" => "http://graph.facebook.com/{$user['id']}/picture?type=large",
				"avatartype" => "remote",
				);
				
			$userhandler->set_data($newUser);
			if($userhandler->validate_user())
			{
				$newUserData = $userhandler->insert_user();
			}
			
			list($maxwidth, $maxheight) = explode("x", my_strtolower($mybb->settings['maxavatardims']));
			
			$extraData = array(
				"avatardimensions" => $maxwidth."|".$maxheight,
				"myfb_uid" => $user['id']
				);
				
			$db->update_query("users", $extraData, "uid = {$newUserData['uid']}");
			
			// after registration we have to logn this new user in
			my_setcookie("mybbuser", $newUserData['uid']."_".$newUserData['loginkey'], null, true);
			redirect("index.php", $lang->redirect_registered);
		}
	}
	// this user has already a linked-to-facebook account, just log him in and update session
	else {
		$db->delete_query("sessions", "ip='".$db->escape_string($session->ipaddress)."' AND sid != '".$session->sid."'");
		$newsession = array(
			"uid" => $facebookID['uid'],
		);
		$db->update_query("sessions", $newsession, "sid='".$session->sid."'");
		
		$syncData = array();
		
		if(!$facebookID['avatar']) {
			list($maxwidth, $maxheight) = explode("x", my_strtolower($mybb->settings['maxavatardims']));
			$syncData = array(
				"avatar" => "http://graph.facebook.com/{$user['id']}/picture?type=large",
				"avatardimensions" => $maxwidth."|".$maxheight,
				"avatartype" => "remote"
				);
		}
		
		if(!empty($syncData)) {
			$db->update_query("users", $syncData, "uid = '{$facebookID['uid']}'");
		}
		
		my_setcookie("mybbuser", $facebookID['uid']."_".$facebookID['loginkey'], null, true);
		my_setcookie("sid", $session->sid, -1, true);
		redirect("index.php", $lang->redirect_loggedin);
	}
	
}

function myfbconnect_debug($data) {
	echo "<pre>";
	echo print_r($data);
	echo "</pre>";
	exit;
}