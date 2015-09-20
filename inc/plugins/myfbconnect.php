<?php

/**
 * A bridge between MyBB with Facebook, featuring login, registration and more.
 *
 * @package MyFacebook Connect
 * @author  Shade <legend_k@live.it>
 * @license http://opensource.org/licenses/mit-license.php MIT license
 * @version 2.3
 */

if (!defined('IN_MYBB')) {
	die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');
}

if (!defined("PLUGINLIBRARY")) {
	define("PLUGINLIBRARY", MYBB_ROOT . "inc/plugins/pluginlibrary.php");
}

function verify_port_443()
{
	global $mybb, $lang;
	
	if ($mybb->input['skip_port_check']) {
		return true;
	}
	
	// 3 seconds timeout to check for port 443 is enough
	$fp = @fsockopen('127.0.0.1', 443, $errno, $errstr, 3);
	
	// Port 443 is closed or blocked
	if (!$fp) {
	
		flash_message($lang->sprintf($lang->myfbconnect_error_port_443_not_open, $mybb->post_code), 'error');
		admin_redirect("index.php?module=config-plugins");
	    
	}
	
	return true;

}

function myfbconnect_info()
{
	return array(
		'name' => 'MyFacebook Connect',
		'description' => 'Integrates MyBB with Facebook, featuring login and registration.',
		'website' => 'https://github.com/Shade-/MyFacebook-Connect',
		'author' => 'Shade',
		'authorsite' => '',
		'version' => '2.3',
		'compatibility' => '16*,17*,18*',
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
	
	verify_port_443();
	
	if (!file_exists(PLUGINLIBRARY)) {
		flash_message($lang->myfbconnect_pluginlibrary_missing, "error");
		admin_redirect("index.php?module=config-plugins");
	}
	
	$PL or require_once PLUGINLIBRARY;
	
	$PL->settings('myfbconnect', $lang->setting_group_myfbconnect, $lang->setting_group_myfbconnect_desc, array(
		'enabled' => array(
			'title' => $lang->setting_myfbconnect_enable,
			'description' => $lang->setting_myfbconnect_enable_desc,
			'value' => '1'
		),
		'appid' => array(
			'title' => $lang->setting_myfbconnect_appid,
			'description' => $lang->setting_myfbconnect_appid_desc,
			'value' => '',
			'optionscode' => 'text'
		),
		'appsecret' => array(
			'title' => $lang->setting_myfbconnect_appsecret,
			'description' => $lang->setting_myfbconnect_appsecret_desc,
			'value' => '',
			'optionscode' => 'text'
		),
		'fastregistration' => array(
			'title' => $lang->setting_myfbconnect_fastregistration,
			'description' => $lang->setting_myfbconnect_fastregistration_desc,
			'value' => '1'
		),
		'usergroup' => array(
			'title' => $lang->setting_myfbconnect_usergroup,
			'description' => $lang->setting_myfbconnect_usergroup_desc,
			'value' => '2',
			'optionscode' => 'text'
		),
		'verifiedonly' => array(
			'title' => $lang->setting_myfbconnect_verifiedonly,
			'description' => $lang->setting_myfbconnect_verifiedonly_desc,
			'value' => '0'
		),
		
		// PM delivery
		'passwordpm' => array(
			'title' => $lang->setting_myfbconnect_passwordpm,
			'description' => $lang->setting_myfbconnect_passwordpm_desc,
			'value' => '1'
		),
		'passwordpm_subject' => array(
			'title' => $lang->setting_myfbconnect_passwordpm_subject,
			'description' => $lang->setting_myfbconnect_passwordpm_subject_desc,
			'optionscode' => 'text',
			'value' => $lang->myfbconnect_default_passwordpm_subject
		),
		'passwordpm_message' => array(
			'title' => $lang->setting_myfbconnect_passwordpm_message,
			'description' => $lang->setting_myfbconnect_passwordpm_message_desc,
			'optionscode' => 'textarea',
			'value' => $lang->myfbconnect_default_passwordpm_message
		),
		'passwordpm_fromid' => array(
			'title' => $lang->setting_myfbconnect_passwordpm_fromid,
			'description' => $lang->setting_myfbconnect_passwordpm_fromid_desc,
			'optionscode' => 'text',
			'value' => ''
		),
		
		// Posting on wall
		'postonwall' => array(
			'title' => $lang->setting_myfbconnect_postonwall,
			'description' => $lang->setting_myfbconnect_postonwall_desc,
			'value' => '0'
		),
		'postonwall_message' => array(
			'title' => $lang->setting_myfbconnect_postonwall_message,
			'description' => $lang->setting_myfbconnect_postonwall_message_desc,
			'optionscode' => 'textarea',
			'value' => $lang->myfbconnect_default_postonwall_message
		),
		
		// Avatar and cover
		'fbavatar' => array(
			'title' => $lang->setting_myfbconnect_fbavatar,
			'description' => $lang->setting_myfbconnect_fbavatar_desc,
			'value' => '1'
		),
		
		// Birthday
		'fbbday' => array(
			'title' => $lang->setting_myfbconnect_fbbday,
			'description' => $lang->setting_myfbconnect_fbbday_desc,
			'value' => '1'
		),
		
		// Location
		'fblocation' => array(
			'title' => $lang->setting_myfbconnect_fblocation,
			'description' => $lang->setting_myfbconnect_fblocation_desc,
			'value' => '1'
		),
		'fblocationfield' => array(
			'title' => $lang->setting_myfbconnect_fblocationfield,
			'description' => $lang->setting_myfbconnect_fblocationfield_desc,
			'optionscode' => 'text',
			'value' => '1'
		),
		
		// Bio
		'fbbio' => array(
			'title' => $lang->setting_myfbconnect_fbbio,
			'description' => $lang->setting_myfbconnect_fbbio_desc,
			'value' => '1'
		),
		'fbbiofield' => array(
			'title' => $lang->setting_myfbconnect_fbbiofield,
			'description' => $lang->setting_myfbconnect_fbbiofield_desc,
			'optionscode' => 'text',
			'value' => '2'
		),
		
		// Sex
		'fbsex' => array(
			'title' => $lang->setting_myfbconnect_fbsex,
			'description' => $lang->setting_myfbconnect_fbsex_desc,
			'value' => '0'
		),
		'fbsexfield' => array(
			'title' => $lang->setting_myfbconnect_fbsexfield,
			'description' => $lang->setting_myfbconnect_fbsexfield_desc,
			'optionscode' => 'text',
			'value' => '3'
		),
		
		// Name and last name
		'fbdetails' => array(
			'title' => $lang->setting_myfbconnect_fbdetails,
			'description' => $lang->setting_myfbconnect_fbdetails_desc,
			'value' => '0'
		),
		'fbdetailsfield' => array(
			'title' => $lang->setting_myfbconnect_fbdetailsfield,
			'description' => $lang->setting_myfbconnect_fbdetailsfield_desc,
			'optionscode' => 'text',
			'value' => ''
		),
	));
	
	$columns_to_check = array('fbavatar', 'fbsex', 'fbdetails', 'fbbio', 'fbbday', 'fblocation', 'bigint(50) NOT NULL DEFAULT 0' => 'myfb_uid');
	$columns_to_add = '';
	
	// Check if columns are already there (this prevents duplicate installation errors)
	foreach ($columns_to_check as $type => $name) {
		
		if (!$db->field_exists($name, 'users')) {
			
			if (is_int($type)) {
				$type = 'int(1) NOT NULL DEFAULT 1';
			}
		
			$columns_to_add .= "`{$name}` $type,";
			
		}
		
	}
	
	$columns_to_add = rtrim($columns_to_add, ',');
	
	// Insert our Facebook columns into the database
	$db->query("ALTER TABLE " . TABLE_PREFIX . "users ADD ({$columns_to_add})");
	
	// Add the report table
	if (!$db->table_exists('myfbconnect_reports')) {
        $collation = $db->build_create_table_collation();
        $db->write_query("CREATE TABLE ".TABLE_PREFIX."myfbconnect_reports(
            id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            dateline VARCHAR(15) NOT NULL,
            code VARCHAR(10) NOT NULL,
            file TEXT,
            line INT(6) NOT NULL,
            message TEXT,
            trace TEXT
            ) ENGINE=MyISAM{$collation};");
    }
	
	// Insert our templates	   
	$dir       = new DirectoryIterator(dirname(__FILE__) . '/MyFacebookConnect/templates');
	$templates = array();
	foreach ($dir as $file) {
		if (!$file->isDot() and !$file->isDir() and pathinfo($file->getFilename(), PATHINFO_EXTENSION) == 'html') {
			$templates[$file->getBasename('.html')] = file_get_contents($file->getPathName());
		}
	}
	
	$PL->templates('myfbconnect', 'MyFacebook Connect', $templates);
	
	// Create cache
	$info                        = myfbconnect_info();
	$shadePlugins                = $cache->read('shade_plugins');
	$shadePlugins[$info['name']] = array(
		'title' => $info['name'],
		'version' => $info['version']
	);
	
	$cache->update('shade_plugins', $shadePlugins);
	
	// Try to update templates
	require_once MYBB_ROOT . 'inc/adminfunctions_templates.php';	
	find_replace_templatesets('header_welcomeblock_guest', '#' . preg_quote('{$lang->welcome_register}</a>') . '#i', '{$lang->welcome_register}</a> &mdash; <a href="{$mybb->settings[\'bburl\']}/myfbconnect.php?action=login">{$lang->myfbconnect_login}</a>');
	
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
	
	// Drop settings
	$PL->settings_delete('myfbconnect');
	$db->drop_table('mygpconnect_reports');
	
	// Delete our columns
	$db->query("ALTER TABLE " . TABLE_PREFIX . "users DROP `fbavatar`, DROP `fbsex`, DROP `fbdetails`, DROP `fbbio`, DROP `fbbday`, DROP `fblocation`, DROP `myfb_uid`");
	
	// Delete the plugin from cache
	$info         = myfbconnect_info();
	$shadePlugins = $cache->read('shade_plugins');
	unset($shadePlugins[$info['name']]);
	$cache->update('shade_plugins', $shadePlugins);
	
	$PL->templates_delete('myfbconnect');
	
	// Try to update templates
	require_once MYBB_ROOT . 'inc/adminfunctions_templates.php';
	find_replace_templatesets('header_welcomeblock_guest', '#' . preg_quote('&mdash; <a href="{$mybb->settings[\'bburl\']}/myfbconnect.php?action=login">{$lang->myfbconnect_login}</a>') . '#i', '');
	
}

global $mybb;

if ($mybb->settings['myfbconnect_enabled']) {
	
	// Global
	$plugins->add_hook('global_start', 'myfbconnect_global');
	
	// User CP
	$plugins->add_hook('usercp_menu', 'myfbconnect_usercp_menu', 40);
	$plugins->add_hook('usercp_start', 'myfbconnect_usercp');
	
	// Who's Online
	$plugins->add_hook("fetch_wol_activity_end", "myfbconnect_fetch_wol_activity");
	$plugins->add_hook("build_friendly_wol_location_end", "myfbconnect_build_wol_location");
	
	// Admin CP
	if (defined('IN_ADMINCP')) {
	
		// Update routines and settings
		$plugins->add_hook("admin_page_output_header", "myfbconnect_update");
		$plugins->add_hook("admin_page_output_footer", "myfbconnect_settings_footer");
		
		// Replace text inputs to select boxes dinamically
		$plugins->add_hook("admin_config_settings_change", "myfbconnect_settings_saver");
		$plugins->add_hook("admin_formcontainer_output_row", "myfbconnect_settings_replacer");
	}
	
}

function myfbconnect_global()
{
	
	global $mybb, $lang, $templatelist;
	
	if ($templatelist) {
		$templatelist = explode(',', $templatelist);
	}
	// Fixes common warnings (due to $templatelist being void)
	else {
		$templatelist = array();
	}
	
	if (THIS_SCRIPT == 'myfbconnect.php') {
	
		$templatelist[] = 'myfbconnect_register';
		$templatelist[] = 'myfbconnect_register_settings_setting';
		
	}
	
	if (THIS_SCRIPT == 'usercp.php') {
		$templatelist[] = 'myfbconnect_usercp_menu';
	}
	
	if (THIS_SCRIPT == 'usercp.php' and $mybb->input['action'] == 'myfbconnect') {
	
		$templatelist[] = 'myfbconnect_usercp_settings';
		$templatelist[] = 'myfbconnect_usercp_settings_linkprofile';
		$templatelist[] = 'myfbconnect_usercp_settings_setting';
		$templatelist[] = 'myfbconnect_usercp_showsettings';
		
	}
	
	$templatelist = implode(',', array_filter($templatelist));
		
	
	$lang->load('myfbconnect');
	
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
			
			$text   = $lang->myfbconnect_settings_whattosync;
			$unlink = "<input type=\"submit\" class=\"button\" name=\"unlink\" value=\"{$lang->myfbconnect_settings_unlink}\" />";
			
			if ($userSettings) {
			
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
				
			}
			else {
				$text = $lang->myfbconnect_settings_connected;
			}
			
		}
		else {
			
			$text = $lang->myfbconnect_settings_linkaccount;
			eval("\$options = \"" . $templates->get('myfbconnect_usercp_settings_linkprofile') . "\";");
			
		}
		
		eval("\$content = \"" . $templates->get('myfbconnect_usercp_settings') . "\";");
		output_page($content);
	}
}

function myfbconnect_update()
{
	global $mybb, $db, $cache, $lang;
	
	// Download report
	if ($mybb->input['export_id'] and $mybb->input['gid'] == myfbconnect_settings_gid()) {
	
		$plugin_info = myfbconnect_info();
	
		$xml = "<?xml version=\"1.0\" encoding=\"{$lang->settings['charset']}\"?".">\r\n";
		$xml .= "<report name=\"".$plugin_info['name']."\" version=\"".$plugin_info['version']."\">\r\n";
		
		$query = $db->simple_select('myfbconnect_reports', '*', 'id = ' . (int) $mybb->input['export_id']);
		while ($report = $db->fetch_array($query)) {
			
			foreach ($report as $k => $v) {
				
				$xml .= "\t\t<{$k}>{$v}</{$k}>\r\n";
				
			}
			
		}
		$xml .= "</report>";
		
		header("Content-disposition: attachment; filename=" . $plugin_info['name'] . "-report-" . $mybb->input['export_id'] . ".xml");
		header("Content-type: application/octet-stream");
		header("Content-Length: ".strlen($xml));
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $xml;
		
		exit;
	}
	
	$file = MYBB_ROOT . "inc/plugins/MyFacebookConnect/class_update.php";
	
	if (file_exists($file)) {
		require_once $file;
	}
}

/**
 * Displays peekers in settings
 **/
function myfbconnect_settings_footer()
{
	global $mybb, $db, $lang;
	
	if ($mybb->input["action"] == "change" and $mybb->request_method != "post") {
	
		$gid = myfbconnect_settings_gid();
		
		if ($mybb->input['gid'] == $gid) {
		
			// Delete reports
			if ($mybb->input['delete_report']) {
				
				switch ($mybb->input['delete_report']) {
					case 'all':
						$db->delete_query('myfbconnect_reports');
						break;
					default:
						$db->delete_query('myfbconnect_reports', 'id = ' . (int) $mybb->input['delete_report']);
				}
				
				flash_message($lang->myfbconnect_success_deleted_reports, 'success');
				admin_redirect('index.php?module=config-settings&action=change&gid=' . $gid);
				
			}
			
			$reports = array();
			$query = $db->simple_select('myfbconnect_reports');
			while ($report = $db->fetch_array($query)) {
				$reports[] = $report;
			}
			
			if ($reports) {
			
				$table = new Table;
				$table->construct_header($lang->myfbconnect_reports_date, array(
					'width' => '15%'
				));
				$table->construct_header($lang->myfbconnect_reports_code, array(
					'width' => '5%'
				));
				$table->construct_header($lang->myfbconnect_reports_file);
				$table->construct_header($lang->myfbconnect_reports_line, array(
					'width' => '5%'
				));
				$table->construct_header($lang->options, array(
					'width' => '10%',
					'style' => 'text-align: center'
				));
				
				foreach ($reports as $report) {
				
					foreach ($report as $k => $val) {
					
						if (in_array($k, array('id', 'message', 'trace'))) {
							continue;
						}
						
						if ($k == 'dateline') {
							$val = my_date($mybb->settings['dateformat'], $val) . ', ' . my_date($mybb->settings['timeformat'], $val);
						}
						
						$table->construct_cell($val);
						
					}
					
					$popup = new PopupMenu("item_{$report['id']}", $lang->options);
					$popup->add_item($lang->myfbconnect_reports_download, 'index.php?module=config-settings&action=change&gid=' . $gid . '&export_id=' . $report['id']);
					$popup->add_item($lang->myfbconnect_reports_delete, 'index.php?module=config-settings&action=change&gid=' . $gid . '&delete_report=' . $report['id']);
					
					$table->construct_cell($popup->fetch(), array(
						'class' => 'align_center'
					));
					
					$table->construct_row();
					
				}
				
				$table->construct_cell('<a href="index.php?module=config-settings&action=change&gid=' . $gid . '&delete_report=' . $report['id'] . '" class="button">' . $lang->myfbconnect_reports_delete_all . '</a>', array(
					'colspan' => 5,
					'class' => 'align_center'
					
				));
				$table->construct_row();
				
				$table->output($lang->myfbconnect_reports);
				
			}
			
		}
		
		if ($mybb->input["gid"] == $gid or !$mybb->input['gid']) {
			
			// 1.8 has jQuery, not Prototype
			if ($mybb->version_code >= 1700) {
				echo '<script type="text/javascript">
	$(document).ready(function() {
		loadMyFBConnectPeekers();
		loadStars();
	});
	function loadMyFBConnectPeekers()
	{
		new Peeker($(".setting_myfbconnect_passwordpm"), $("#row_setting_myfbconnect_passwordpm_subject"), /1/, true);
		new Peeker($(".setting_myfbconnect_passwordpm"), $("#row_setting_myfbconnect_passwordpm_message"), /1/, true);
		new Peeker($(".setting_myfbconnect_passwordpm"), $("#row_setting_myfbconnect_passwordpm_fromid"), /1/, true);
		new Peeker($(".setting_myfbconnect_fbbio"), $("#row_setting_myfbconnect_fbbiofield"), /1/, true);
		new Peeker($(".setting_myfbconnect_fblocation"), $("#row_setting_myfbconnect_fblocationfield"), /1/, true);
		new Peeker($(".setting_myfbconnect_fbdetails"), $("#row_setting_myfbconnect_fbdetailsfield"), /1/, true);
		new Peeker($(".setting_myfbconnect_fbsex"), $("#row_setting_myfbconnect_fbsexfield"), /1/, true);
		new Peeker($(".setting_myfbconnect_postonwall"), $("#row_setting_myfbconnect_postonwall_message"), /1/, true);
	}
	function loadStars()
	{
		add_star("row_setting_myfbconnect_appid");
		add_star("row_setting_myfbconnect_appsecret");
	}
	</script>';
			}
			else {
				echo '<script type="text/javascript">
	Event.observe(window, "load", function() {
		loadMyFBConnectPeekers();
		loadStars();
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
		new Peeker($$(".setting_myfbconnect_postonwall"), $("row_setting_myfbconnect_postonwall_message"), /1/, true);
	}
	function loadStars()
	{
		add_star("row_setting_myfbconnect_appid");
		add_star("row_setting_myfbconnect_appsecret");
	}
	</script>';
			}

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
	$gid   = (int) $db->fetch_field($query, "gid");
	
	return $gid;
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

$GLOBALS['replace_custom_fields'] = array('fblocationfield', 'fbbiofield', 'fbdetailsfield', 'fbsexfield');

function myfbconnect_settings_saver()
{
	global $mybb, $page, $replace_custom_fields;

	if ($mybb->request_method == "post" and $mybb->input['upsetting'] and $page->active_action == "settings" and $mybb->input['gid'] == myfbconnect_settings_gid()) {
	
		foreach ($replace_custom_fields as $setting) {
		
			$parentfield = str_replace('field', '', $setting);
			
			$mybb->input['upsetting']['myfbconnect_'.$setting] = $mybb->input['myfbconnect_'.$setting.'_select'];
			
			// Reset parent field if empty
			if (!$mybb->input['upsetting']['myfbconnect_'.$setting]) {
				$mybb->input['upsetting']['myfbconnect_'.$parentfield] = 0;
			}
		}
		
		$mybb->input['upsetting']['myfbconnect_usergroup'] = $mybb->input['myfbconnect_usergroup_select'];
			
	}
}

function myfbconnect_settings_replacer($args)
{
	global $db, $lang, $form, $mybb, $page, $replace_custom_fields;

	if ($page->active_action != "settings" and $mybb->input['action'] != "change" and $mybb->input['gid'] != myfbconnect_settings_gid()) {
		return false;
	}
        
	$query = $db->simple_select('profilefields', 'name, fid');
	
	$profilefields = array('' => '');
	
	while ($field = $db->fetch_array($query)) {
		$profilefields[$field['fid']] = $field['name'];
	}
	$db->free_result($query);
	
	foreach ($replace_custom_fields as $setting) {
	
		if ($args['row_options']['id'] == "row_setting_myfbconnect_".$setting) {
	
			if (!$profilefields) {
				
				$args['content'] = $lang->myfbconnect_select_nofieldsavailable;
				
				continue;
				
			}
			
			$tempKey = 'myfbconnect_'.$setting;
			
			// Replace the textarea with a cool selectbox
			$args['content'] = $form->generate_select_box($tempKey."_select", $profilefields, $mybb->settings[$tempKey]);
			
		}
		
	}
		
	if ($args['row_options']['id'] == "row_setting_myfbconnect_usergroup") {
			
		$tempKey = 'myfbconnect_usergroup';
			
		// Replace the textarea with a cool selectbox
		$args['content'] = $form->generate_group_select($tempKey."_select", array($mybb->settings[$tempKey]));
			
	}
}