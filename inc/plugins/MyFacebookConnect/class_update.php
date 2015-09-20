<?php

/**
 * Upgrading routines
 */

class MyFacebook_Update
{
	
	private $version;
	
	private $old_version;
	
	private $plugins;
	
	private $info;
	
	public function __construct()
	{
		
		global $mybb, $db, $cache, $lang;
		
		if (!$lang->myfbconnect) {
			$lang->load("myfbconnect");
		}
		
		$this->load_version();
		
		$check = $this->check_update();
		
		if ($mybb->input['update'] == 'myfbconnect' and $check) {
			$this->update();
		}
		
	}
	
	private function load_version()
	{
		global $cache;
		
		$this->info        = myfbconnect_info();
		$this->plugins     = $cache->read('shade_plugins');
		$this->old_version = $this->plugins[$this->info['name']]['version'];
		$this->version     = $this->info['version'];
		
	}
	
	private function check_update()
	{
		global $lang, $mybb;
		
		if (version_compare($this->old_version, $this->version, "<")) {
			
			if ($mybb->input['update']) {
				return true;
			} else {
				flash_message($lang->myfbconnect_error_needtoupdate, "error");
			}
			
		}
		
		return false;
		
	}
	
	private function update()
	{
		global $db, $mybb, $cache, $lang;
		
		$new_settings = $drop_settings = array();
				
		// Get the gid
		$query = $db->simple_select("settinggroups", "gid", "name='myfbconnect'");
		$gid   = (int) $db->fetch_field($query, "gid");
		
		// 1.0.1
		if (version_compare($this->old_version, '1.0.1', "<")) {
			
			require_once MYBB_ROOT . "inc/adminfunctions_templates.php";
			find_replace_templatesets('myfbconnect_register', '#' . preg_quote('<td valign="top">') . '#i', '<td valign="top">{$errors}');
			
		}
		
		// 1.0.3
		if (version_compare($this->old_version, '1.0.3', "<")) {
			
			$new_settings[] = array(
				"name" => "myfbconnect_verifiedonly",
				"title" => $db->escape_string($lang->setting_myfbconnect_verifiedonly),
				"description" => $db->escape_string($lang->setting_myfbconnect_verifiedonly_desc),
				"optionscode" => "yesno",
				"value" => 0,
				"disporder" => 7,
				"gid" => $gid
			);
			
		}
		
		// 1.1
		if (version_compare($this->old_version, '1.1', "<")) {
			
			$new_settings[] = array(
				"name" => "myfbconnect_fbavatar",
				"title" => $db->escape_string($lang->setting_myfbconnect_fbavatar),
				"description" => $db->escape_string($lang->setting_myfbconnect_fbavatar_desc),
				"optionscode" => "yesno",
				"value" => 1,
				"disporder" => 12,
				"gid" => $gid
			);
			
			require_once MYBB_ROOT . "inc/adminfunctions_templates.php";
			find_replace_templatesets('myfbconnect_usercp_settings', '#' . preg_quote('<input type="submit" value="{$lang->myfbconnect_settings_save}" />') . '#i', '<input type="submit" class=\"button\" value="{$lang->myfbconnect_settings_save}" />{$unlink}');
			
		}
		
		// 2.0
		if (version_compare($this->old_version, '2.0', "<")) {
			
			$drop_settings[] = "requestpublishingperms";
			
			$new_settings[] = array(
				"name" => "myfbconnect_postonwall",
				"title" => $db->escape_string($lang->setting_myfbconnect_postonwall),
				"description" => $db->escape_string($lang->setting_myfbconnect_postonwall_desc),
				"optionscode" => "yesno",
				"value" => 0,
				"disporder" => 30,
				"gid" => $gid
			);
			
			$new_settings[] = array(
				"name" => "myfbconnect_postonwall_message",
				"title" => $db->escape_string($lang->setting_myfbconnect_postonwall_message),
				"description" => $db->escape_string($lang->setting_myfbconnect_postonwall_message_desc),
				"optionscode" => "textarea",
				"value" => $lang->myfbconnect_default_postonwall_message,
				"disporder" => 31,
				"gid" => $gid
			);
			
			// Let's at least try to change that, anyway, 2.0 has backward compatibility so it doesn't matter if this fails
			require_once MYBB_ROOT . "inc/adminfunctions_templates.php";
			find_replace_templatesets('header_welcomeblock_guest', '#' . preg_quote('fblogin') . '#i', 'login');
			
		}
		
		// 2.3
		if (version_compare($this->old_version, '2.3', "<")) {
			
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
			
		}
		
		if ($new_settings) {
			$db->insert_query_multiple('settings', $new_settings);
		}
		
		if ($drop_settings) {
			$db->delete_query('settings', "name IN ('myfbconnect_". implode("','myfbconnect_", $drop_settings) ."')");
		}
		
		rebuild_settings();
		
		// Update the current version number and redirect
		$this->plugins[$this->info['name']] = array(
			'title' => $this->info['name'],
			'version' => $this->version
		);
		
		$cache->update('shade_plugins', $this->plugins);
		
		flash_message($lang->sprintf($lang->myfbconnect_success_updated, $this->old_version, $this->version), "success");
		admin_redirect($_SERVER['HTTP_REFERER']);
		
	}
	
	/**
	 * Debugs any type of data, printing out an array and immediately killing the execution of the currently running script
	 */
	public function debug($data)
	{
		// Fallback for arrays
		if (is_array($data)) {
			$data = array_map('htmlspecialchars_uni', $data);
		}
		// Fallback for strings
		else if (is_string($data)) {
			$data = htmlspecialchars_uni($data);
		}
		
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		
		exit;
	}
	
}

// Direct init on call
$FacebookConnectUpdate = new MyFacebook_Update();