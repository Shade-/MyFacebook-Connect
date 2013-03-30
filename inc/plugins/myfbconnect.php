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
        )
    ));
	
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
	global $db, $PL, $cache;
	
	if (!file_exists(PLUGINLIBRARY)) {
		flash_message($lang->myfbconnect_pluginlibrary_missing, "error");
		admin_redirect("index.php?module=config-plugins");
	}
	
	$PL or require_once PLUGINLIBRARY;
	
	$PL->settings_delete('myfbconnect');
	
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
	$plugins->add_hook('member_do_register_end', 'myfbconnect_run');
}

function myfbconnect_run() {
}