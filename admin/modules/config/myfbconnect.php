<?php

if (!defined("IN_MYBB")) {
        header("HTTP/1.0 404 Not Found");
        exit;
}

define(MODULE, "myfbconnect");

$lang->load("myfbconnect");

$page->add_breadcrumb_item($lang->myfbconnect_file_status, "index.php?module=config-myfbconnect");

$gid = myfbconnect_settings_gid();

$sub_tabs['filestatus'] = array(
	'title' => $lang->myfbconnect_general,
	'link' => "index.php?module=config-myfbconnect",
	'description' => $lang->myfbconnect_general_desc
);
$sub_tabs['settings'] = array(
	'title' => $lang->myfbconnect_settings,
	'link' => "index.php?module=config-settings&action=change&gid={$gid}"
);

$page->output_header($lang->myfbconnect);

if (!$mybb->input['action']) {

		$page->output_nav_tabs($sub_tabs, 'filestatus');
        
        if ($mybb->settings['myfbconnect_appid'] and $mybb->settings['myfbconnect_appsecret']) {
        	$page->output_success($lang->myfbconnect_api_ok);
        }
        else if (!$mybb->settings['myfbconnect_appid']) {
        	$page->output_error($lang->myfbconnect_api_missingid);
        }
        else if (!$mybb->settings['myfbconnect_appsecret']) {
        	$page->output_error($lang->myfbconnect_api_missingsecret);
        }
        else {
        	$page->output_error($lang->myfbconnect_api_notconfigured);
        }

        $table = new Table;
        $table->construct_header($lang->myfbconnect_file);
        $table->construct_header($lang->myfbconnect_status, array('style' => 'text-align: center'));
        
        // All our files we need to check for integrity
        $files = array(
        	'' => array(
        		'myfbconnect.php' => 1
        	),
        	'admin/modules/config' => array(
        		'myfbconnect.php' => 0
        	),
        	'images/usercp' => array(
        		'facebook.png' => 0
        	),
        	'inc/plugins' => array(
        		'myfbconnect.php' => 1
        	),
        	'inc/plugins/MyFacebookConnect' => array(
        		'class_facebook.php' => 1,
        		'class_update.php' => 0
        	),
        	'inc/plugins/MyFacebookConnect/templates' => array(
        		'register.html' => 0,
        		'register_settings_setting.html' => 0,
        		'usercp_menu.html' => 0,
        		'usercp_settings.html' => 0,
        		'usercp_settings_linkprofile.html' => 0,
        		'usercp_settings_setting.html' => 0
        	),
        	'myfbconnect/src' => array(
        		'facebook.php' => 1,
        		'base_facebook.php' => 1,
        		'fb_ca_chain_bundle.crt' => 1,
        	),
        );
        
        $missing = 0;
                
        foreach ($files as $dir => $array) {
	        
	        if (is_dir(MYBB_ROOT . $dir)) {
				
				$directory = $dir;
				
				if (!$directory) {
					$directory = "/";
				}
				
				$table->construct_cell($directory, array('colspan' => '2'));
				$table->construct_row();
		        
		        foreach ($array as $file => $harmful) {
		        
		        	$status = '<img src="'. $mybb->settings['bburl'] . '/images/valid.gif" />';
		        	$path = MYBB_ROOT . $dir . "/" . $file;
		        			        
			        if (!file_exists($path)) {
			        	
			        	if ($harmful) {
		        			$harm = 1;
		        		}
		        		
		        		$status = '<img src="'. $mybb->settings['bburl'] . '/images/error.gif" />';
				        $missing = 1;
				        
			        }
					
					$table->construct_cell($file, array('style' => 'padding-left: 55px'));
					$table->construct_cell($status, array('style' => 'text-align: center'));
					$table->construct_row();
			    
		        }
		        
	        }
	        
        }
        
        if (!$missing) {
        	$page->output_success($lang->myfbconnect_status_ok);
        }
        else {
        	if ($harm) {
	        	$page->output_error($lang->myfbconnect_status_notok);
        	}
        	else {
        		$page->output_error($lang->myfbconnect_status_notok_harm);
        	}
        }
        
        $table->output($lang->myfbconnect_file_status);
        
        
}

$page->output_footer();

function my_debug($data) {
	echo "<pre>";
	print_r($data);
	exit;
}