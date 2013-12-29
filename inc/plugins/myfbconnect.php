<?php

/**
 * MyFacebook Connect
 * 
 * A bridge between MyBB with Facebook, featuring login, registration and more.
 *
 * @package MyFacebook Connect
 * @author  Shade <legend_k@live.it>
 * @license http://opensource.org/licenses/mit-license.php MIT license
 * @version 2.0
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
		'authorsite' => '',
		'version' => '2.0',
		'compatibility' => '16*',
		'guid' => 'c5627aab08ec4d321e71afd2b9d02fb2'
	);
}

function myfbconnect_is_installed()
{
	global $cache;
	
	$info      = myfbconnect_info();
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
		'verifiedonly' => array(
			'title' => $lang->myfbconnect_settings_verifiedonly,
			'description' => $lang->myfbconnect_settings_verifiedonly_desc,
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
		// avatar and cover
		'fbavatar' => array(
			'title' => $lang->myfbconnect_settings_fbavatar,
			'description' => $lang->myfbconnect_settings_fbavatar_desc,
			'value' => '1'
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
		// sex
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
	$dir       = new DirectoryIterator(dirname(__FILE__) . '/MyFacebookConnect/templates');
	$templates = array();
	foreach ($dir as $file) {
		if (!$file->isDot() AND !$file->isDir() AND pathinfo($file->getFilename(), PATHINFO_EXTENSION) == 'html') {
			$templates[$file->getBasename('.html')] = file_get_contents($file->getPathName());
		}
	}
	
	$PL->templates('myfbconnect', 'MyFacebook Connect', $templates);
	
	// create cache
	$info                        = myfbconnect_info();
	$shadePlugins                = $cache->read('shade_plugins');
	$shadePlugins[$info['name']] = array(
		'title' => $info['name'],
		'version' => $info['version']
	);
	$cache->update('shade_plugins', $shadePlugins);
	
	require_once MYBB_ROOT . 'inc/adminfunctions_templates.php';
	
	find_replace_templatesets('header_welcomeblock_guest', '#' . preg_quote('{$lang->welcome_register}</a>') . '#i', '{$lang->welcome_register}</a> &mdash; <a href="{$mybb->settings[\'bburl\']}/myfbconnect.php?action=login">{$lang->myfbconnect_login}</a>');
	
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
	
	$info         = myfbconnect_info();
	// delete the plugin from cache
	$shadePlugins = $cache->read('shade_plugins');
	unset($shadePlugins[$info['name']]);
	$cache->update('shade_plugins', $shadePlugins);
	
	$PL->templates_delete('myfbconnect');
	
	require_once MYBB_ROOT . 'inc/adminfunctions_templates.php';
	
	find_replace_templatesets('header_welcomeblock_guest', '#' . preg_quote('&mdash; <a href="{$mybb->settings[\'bburl\']}/myfbconnect.php?action=login">{$lang->myfbconnect_login}</a>') . '#i', '');
	
	// rebuild settings
	rebuild_settings();
}

global $mybb, $settings;

if ($settings['myfbconnect_enabled']) {
	$plugins->add_hook('global_start', 'myfbconnect_global');
	$plugins->add_hook('usercp_menu', 'myfbconnect_usercp_menu', 40);
	$plugins->add_hook('usercp_start', 'myfbconnect_usercp');
	$plugins->add_hook("admin_page_output_footer", "myfbconnect_settings_footer");
	$plugins->add_hook("fetch_wol_activity_end", "myfbconnect_fetch_wol_activity");
	$plugins->add_hook("build_friendly_wol_location_end", "myfbconnect_build_wol_location");
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
		$templatelist .= 'myfbconnect_register,myfbconnect_register_settings_setting';
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
	
	global $mybb, $lang, $inlinesuccess;
	
	// Load API in certain areas
	if (in_array($mybb->input['action'], array('fblink','do_fblink')) or $_SESSION['fblogin'] or ($mybb->input['action'] == 'myfbconnect' and $mybb->request_method == 'post')) {
		
		require_once MYBB_ROOT . "inc/plugins/MyFacebookConnect/class_facebook.php";
		$FacebookConnect = new MyFacebook();
		
	}
	
	$settingsToCheck = array(
		'fbavatar',
		'fbbday',
		'fbsex',
		'fbdetails',
		'fbbio',
		'fblocation'
	);
	
	if (!$lang->myfbconnect) {
		$lang->load('myfbconnect');
	}
	
	// Authenticate
	if ($mybb->input['action'] == 'fblink') {
		
		$FacebookConnect->set_fallback('usercp.php?action=do_fblink');
		$FacebookConnect->authenticate();
		
	}
	
	// Link account to his Facebook's one
	if ($mybb->input['action'] == 'do_fblink') {
		
		if (!$FacebookConnect->check_user()) {
			error($lang->myfbconnect_error_noauth);
		}
		
		$user = $FacebookConnect->get_user('id,verified');
		
		if ($user) {
			$FacebookConnect->link_user('', $user['id']);
		}
		
		$FacebookConnect->redirect('usercp.php?action=myfbconnect', '', $lang->myfbconnect_success_linked);
	}
	
	// Settings page
	if ($mybb->input['action'] == 'myfbconnect') {
		
		global $db, $theme, $templates, $headerinclude, $header, $footer, $plugins, $usercpnav;
		
		add_breadcrumb($lang->nav_usercp, 'usercp.php');
		add_breadcrumb($lang->myfbconnect_page_title, 'usercp.php?action=myfbconnect');
		
		// The user is changing his settings
		if ($mybb->request_method == 'post' or $_SESSION['fblogin']) {
			
			if ($mybb->request_method == 'post') {
				verify_post_check($mybb->input['my_post_key']);
			}
			
			// He's unlinking his account
			if ($mybb->input['unlink']) {
				
				$FacebookConnect->unlink_user();
				redirect('usercp.php?action=myfbconnect', $lang->myfbconnect_success_accunlinked, $lang->myfbconnect_success_accunlinked_title);
				
			}
			// He's updating his settings
			else {
				
				$settings = array();
				
				foreach ($settingsToCheck as $setting) {
					
					$settings[$setting] = 0;
					
					if ($mybb->input[$setting] == 1) {
						$settings[$setting] = 1;
					}
					
					// Build a list of parameters to include in the fallback URL
					$loginUrlExtra .= "&{$setting}=" . $settings[$setting];
					
				}
				
				// This user is not logged in with Facebook
				if (!$FacebookConnect->check_user()) {
					
					// Store a token in the session, we will check for it in the next call
					$_SESSION['fblogin'] = 1;
					
					$FacebookConnect->set_fallback("usercp.php?action=myfbconnect" . $loginUrlExtra);
					$FacebookConnect->authenticate();
					
					return;
				}
				
				if ($db->update_query('users', $settings, 'uid = ' . (int) $mybb->user['uid'])) {
					
					unset($_SESSION['fblogin']);
					
					$newUser = array_merge($mybb->user, $settings);
					$FacebookConnect->sync($newUser);
					
					redirect('usercp.php?action=myfbconnect', $lang->myfbconnect_success_settingsupdated, $lang->myfbconnect_success_settingsupdated_title);
					
				}
			}
		}
		
		$options = '';
		if ($mybb->user['myfb_uid']) {
			
			$text   = $lang->myfbconnect_settings_whattosync;
			$unlink = "<input type=\"submit\" class=\"button\" name=\"unlink\" value=\"{$lang->myfbconnect_settings_unlink}\" />";
			
			$userSettings = array();
			
			// Checking if admins and users want to sync that stuff
			foreach ($settingsToCheck as $setting) {
				
				$tempKey = 'myfbconnect_' . $setting;
				
				if (!$mybb->settings[$tempKey]) {
					continue;
				}
				
				$userSettings[$setting] = 0;
				
				if ($mybb->user[$setting]) {
					$userSettings[$setting] = 1;
				}
				
			}
			
			$settings = '';
			foreach ($userSettings as $setting => $value) {
				
				$tempKey = 'myfbconnect_settings_' . $setting;
				
				$checked = '';
				
				if ($value) {
					$checked = " checked=\"checked\"";
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
 * Displays peekers in settings
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
 **/
function myfbconnect_settings_gid()
{
	global $db;
	
	$query = $db->simple_select("settinggroups", "gid", "name = 'myfbconnect'", array(
		"limit" => 1
	));
	$gid   = $db->fetch_field($query, "gid");
	
	return intval($gid);
}

function myfbconnect_fetch_wol_activity(&$user_activity)
{
	global $user, $mybb;
	
	// get the base filename
	$split_loc = explode(".php", $user_activity['location']);
	if ($split_loc[0] == $user['location']) {
		$filename = '';
	} else {
		$filename = my_substr($split_loc[0], -my_strpos(strrev($split_loc[0]), "/"));
	}
	
	// get parameters of the URI
	if ($split_loc[1]) {
		$temp = explode("&amp;", my_substr($split_loc[1], 1));
		foreach ($temp as $param) {
			$temp2                 = explode("=", $param, 2);
			$temp2[0]              = str_replace("amp;", '', $temp2[0]);
			$parameters[$temp2[0]] = $temp2[1];
		}
	}
	
	// if our plugin is found, store our custom vars in the main $user_activity array
	switch ($filename) {
		case "myfbconnect":
			if ($parameters['action']) {
				$user_activity['activity'] = $parameters['action'];
			}
			break;
	}
	
	return $user_activity;
}

function myfbconnect_build_wol_location(&$plugin_array)
{
	global $lang;
	
	$lang->load('myfbconnect');
	
	// let's see what action we are watching
	switch ($plugin_array['user_activity']['activity']) {
		case "login":
		case "do_login":
			$plugin_array['location_name'] = $lang->myfbconnect_viewing_loggingin;
			break;
		case "register":
			$plugin_array['location_name'] = $lang->myfbconnect_viewing_registering;
			break;
	}
	return $plugin_array;
}

/********************************************************************************************************
 *
 * ON-THE-FLY UPGRADING SYSTEM: used to upgrade from any older version to any newer version of the plugin
 *
 ********************************************************************************************************/

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
	$info           = myfbconnect_info();
	$shadePlugins   = $cache->read('shade_plugins');
	$oldversion     = $shadePlugins[$info['name']]['version'];
	$currentversion = $info['version'];
	
	// you need to update buddy!
	if (version_compare($oldversion, $currentversion, "<")) {
		flash_message($lang->myfbconnect_error_needtoupdate, "error");
	}
	
	// you are updating, that's nice!
	if ($mybb->input['upgrade'] == "myfbconnect") {
		// but let's check if you should upgrade first
		if (version_compare($oldversion, $currentversion, "<")) {
			// yeah you should
			// to 1.0.1
			if (version_compare($oldversion, "1.0.1", "<")) {
				require_once MYBB_ROOT . "inc/adminfunctions_templates.php";
				find_replace_templatesets('myfbconnect_register', '#' . preg_quote('<td valign="top">') . '#i', '<td valign="top">{$errors}');
			}
			// to 1.0.3
			if (version_compare($oldversion, "1.0.3", "<")) {
				// get the gid of the settings group
				$query = $db->simple_select("settinggroups", "gid", "name='myfbconnect'");
				$gid   = (int) $db->fetch_field($query, "gid");
				
				$newsetting = array(
					"name" => "myfbconnect_verifiedonly",
					"title" => $db->escape_string($lang->myfbconnect_settings_verifiedonly),
					"description" => $db->escape_string($lang->myfbconnect_settings_verifiedonly_desc),
					"optionscode" => "yesno",
					"value" => "0",
					"disporder" => "7",
					"gid" => $gid
				);
				// add the new setting
				$db->insert_query("settings", $newsetting);
				
				// rebuild settings and here we go!
				rebuild_settings();
			}
			// to 1.1
			if (version_compare($oldversion, "1.1", "<")) {
				// get the gid of the settings group
				$query = $db->simple_select("settinggroups", "gid", "name='myfbconnect'");
				$gid   = (int) $db->fetch_field($query, "gid");
				
				$newsetting = array(
					"name" => "myfbconnect_fbavatar",
					"title" => $db->escape_string($lang->myfbconnect_settings_fbavatar),
					"description" => $db->escape_string($lang->myfbconnect_settings_fbavatar_desc),
					"optionscode" => "yesno",
					"value" => "1",
					"disporder" => "12",
					"gid" => $gid
				);
				// add the new setting
				$db->insert_query("settings", $newsetting);
				
				// rebuild settings
				rebuild_settings();
				require_once MYBB_ROOT . "inc/adminfunctions_templates.php";
				find_replace_templatesets('myfbconnect_usercp_settings', '#' . preg_quote('<input type="submit" value="{$lang->myfbconnect_settings_save}" />') . '#i', '<input type="submit" class=\"button\" value="{$lang->myfbconnect_settings_save}" />{$unlink}');
			}
			// to 1.2
			if (version_compare($oldversion, "1.2", "<")) {
				$updated_setting = array(
					"description" => $db->escape_string($lang->myfbconnect_settings_fbsex_desc)
				);
				// update the setting
				$db->update_query("settings", $newsetting, "name = 'myfbconnect_fbsex'");
				
				// rebuild settings
				rebuild_settings();
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