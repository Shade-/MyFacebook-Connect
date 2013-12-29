<?php

if (!defined("IN_MYBB")) {
        header("HTTP/1.0 404 Not Found");
        exit;
}

define(MODULE, "myfbconnect");

$page->output_header("MyFacebook Connect");

if (!$mybb->input['action']) {

        $table = new Table;
        $table->construct_header("File");
        $table->construct_header("Status", array('style' => 'text-align: center'));
        
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
		        			$status = '<img src="'. $mybb->settings['bburl'] . '/images/error.gif" />';
		        			$harm = 1;
		        		}
		        		else {
		        			$status = '<img src="'. $mybb->settings['bburl'] . '/images/invalid.gif" />';			        		
		        		}
				        $missing = 1;
				        
			        }
					
					$table->construct_cell($file, array('style' => 'padding-left: 55px'));
					$table->construct_cell($status, array('style' => 'text-align: center'));
					$table->construct_row();
			        
		        }
		        
	        }
	        
        }
        
        if (!$missing) {
        	$page->output_success("All files are present and MyFacebook Connect is fully operative.");
        }
        else {
        	if ($harm) {
	        	$page->output_error("Some files are missing, and some of them are critical for MyFacebook Connect's work. Please add them as soon as possible.");
        	}
        	else {
        		$page->output_error("Some files are missing. Please add them as soon as possible.");
        	}
        }

        $table->output("File status");
}
$page->output_footer();

function my_debug($data) {
	echo "<pre>";
	print_r($data);
	exit;
}