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

		$new_settings = $drop_settings = [];
		$updateTemplates = 0;

		// Get the gid
		$query = $db->simple_select("settinggroups", "gid", "name='myfbconnect'");
		$gid   = (int) $db->fetch_field($query, "gid");

		// 1.0.1
		if (version_compare($this->old_version, '1.0.1', "<")) {
			$updateTemplates = 1;
		}

		// 1.0.3
		if (version_compare($this->old_version, '1.0.3', "<")) {

			$new_settings[] = [
				"name" => "myfbconnect_verifiedonly",
				"title" => $db->escape_string($lang->setting_myfbconnect_verifiedonly),
				"description" => $db->escape_string($lang->setting_myfbconnect_verifiedonly_desc),
				"optionscode" => "yesno",
				"value" => 0,
				"disporder" => 7,
				"gid" => $gid
			];

		}

		// 1.1
		if (version_compare($this->old_version, '1.1', "<")) {

			$new_settings[] = [
				"name" => "myfbconnect_fbavatar",
				"title" => $db->escape_string($lang->setting_myfbconnect_fbavatar),
				"description" => $db->escape_string($lang->setting_myfbconnect_fbavatar_desc),
				"optionscode" => "yesno",
				"value" => 1,
				"disporder" => 12,
				"gid" => $gid
			];

			$updateTemplates = 1;

		}

		// 2.0
		if (version_compare($this->old_version, '2.0', "<")) {

			$drop_settings[] = "requestpublishingperms";

			$new_settings[] = [
				"name" => "myfbconnect_postonwall",
				"title" => $db->escape_string($lang->setting_myfbconnect_postonwall),
				"description" => $db->escape_string($lang->setting_myfbconnect_postonwall_desc),
				"optionscode" => "yesno",
				"value" => 0,
				"disporder" => 30,
				"gid" => $gid
			];

			$new_settings[] = [
				"name" => "myfbconnect_postonwall_message",
				"title" => $db->escape_string($lang->setting_myfbconnect_postonwall_message),
				"description" => $db->escape_string($lang->setting_myfbconnect_postonwall_message_desc),
				"optionscode" => "textarea",
				"value" => $lang->myfbconnect_default_postonwall_message,
				"disporder" => 31,
				"gid" => $gid
			];

			$updateTemplates = 1;

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

		// 3.0
		if (version_compare($this->old_version, '3.0', "<")) {

			$drop_settings[] = 'postonwall';
			$drop_settings[] = 'postonwall_message';

			$new_settings[] = [
				"name" => "myfbconnect_keeprunning",
				"title" => $db->escape_string($lang->setting_myfbconnect_keeprunning),
				"description" => $db->escape_string($lang->setting_myfbconnect_keeprunning_desc),
				"optionscode" => "yesno",
				"value" => 0,
				"disporder" => 7,
				"gid" => $gid
			];

			$updateTemplates = 1;

			// New column definition to standardize and anonymize identifiers
			$db->modify_column('users', 'myfb_uid', 'VARCHAR(32) NOT NULL DEFAULT 0');

		}

		// 3.3
		if (version_compare($this->old_version, '3.3', "<")) {

			if ($db->field_exists('fbbio', 'users')) {
				$db->drop_column('users', 'fbbio');
			}

			$drop_settings[] = 'fbbio';
			$drop_settings[] = 'fbbiofield';

			$new_settings[] = [
				"name" => "myfbconnect_scopes",
				"title" => $db->escape_string($lang->setting_myfbconnect_scopes),
				"description" => $db->escape_string($lang->setting_myfbconnect_scopes_desc),
				"optionscode" => "text",
				"value" => "user_location,user_birthday",
				"disporder" => 8,
				"gid" => $gid
			];

		}

		// 3.4
		if (version_compare($this->old_version, '3.4', "<")) {

			$new_settings[] = [
				"name" => "myfbconnect_use_secondary",
				"title" => $db->escape_string($lang->setting_myfbconnect_use_secondary),
				"description" => $db->escape_string($lang->setting_myfbconnect_use_secondary_desc),
				"optionscode" => "yesno",
				"value" => "1",
				"disporder" => 6,
				"gid" => $gid
			];

		}

		if ($new_settings) {
			$db->insert_query_multiple('settings', $new_settings);
		}

		if ($drop_settings) {
			$db->delete_query('settings', "name IN ('myfbconnect_". implode("','myfbconnect_", $drop_settings) ."')");
		}

		rebuild_settings();

		if ($updateTemplates) {

			$PL or require_once PLUGINLIBRARY;

			// Update templates
			$dir       = new DirectoryIterator(dirname(__FILE__) . '/templates');
			$templates = [];
			foreach ($dir as $file) {
				if (!$file->isDot() and !$file->isDir() and pathinfo($file->getFilename(), PATHINFO_EXTENSION) == 'html') {
					$templates[$file->getBasename('.html')] = file_get_contents($file->getPathName());
				}
			}

			$PL->templates('myfbconnect', 'MyFacebook Connect', $templates);

		}

		// Update the current version number and redirect
		$this->plugins[$this->info['name']] = [
			'title' => $this->info['name'],
			'version' => $this->version
		];

		$cache->update('shade_plugins', $this->plugins);

		flash_message($lang->sprintf($lang->myfbconnect_success_updated, $this->old_version, $this->version), "success");
		admin_redirect('index.php');

	}
}

// Direct init on call
$FacebookConnectUpdate = new MyFacebook_Update();