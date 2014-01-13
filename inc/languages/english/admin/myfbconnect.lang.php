<?php
// Installation
$l['myfbconnect'] = "MyFacebook Connect";
$l['myfbconnect_pluginlibrary_missing'] = "<a href=\"http://mods.mybb.com/view/pluginlibrary\">PluginLibrary</a> is missing. Please install it before doing anything else with myfbconnect.";

// Settings - 
$l['setting_group_myfbconnect'] = "Facebook login and registration settings";
$l['setting_group_myfbconnect_desc'] = "Here you can manage Facebook login and registration on your board, changing API keys and options to enable or disable certain aspects of MyFacebook Connect plugin.";
$l['setting_myfbconnect_enable'] = "Master switch";
$l['setting_myfbconnect_enable_desc'] = "Do you want to let your users login and register with Facebook? If an user is already registered the account will be linked to its Facebook account.";
$l['setting_myfbconnect_appid'] = "App ID";
$l['setting_myfbconnect_appid_desc'] = "Enter your App ID token from Facebook Developers site. This will be used together with the secret token to ask authorizations to your users through your app.";
$l['setting_myfbconnect_appsecret'] = "App Secret";
$l['setting_myfbconnect_appsecret_desc'] = "Enter your App Secret token from Facebook Developers site. This will be used together with the ID token to ask authorizations to your users through your app.";
$l['setting_myfbconnect_fastregistration'] = "One-click registration";
$l['setting_myfbconnect_fastregistration_desc'] = "If this option is disabled, when an user wants to register with Facebook he will be asked for permissions for your app if it's the first time he is logging in, otherwise he will be registered and logged in immediately without asking for username changes and what data to sync.";
$l['setting_myfbconnect_usergroup'] = "After registration usergroup";
$l['setting_myfbconnect_usergroup_desc'] = "Select the after-registration usergroup. The user will be inserted directly into this usergroup upon registering. Also, if an existing user links his account to Facebook, this usergroup will be added to his additional groups list.";
$l['setting_myfbconnect_verifiedonly'] = "Allow only verified accounts";
$l['setting_myfbconnect_verifiedonly_desc'] = "Enable this option to restrict only verified Facebook users to register with Facebook. Verified users are those who confirmed their Facebook registration either with a SMS confirmation or registering with a mobile phone, so they are real people. This option is disabled by default but should be enabled in case you want to ensure that only real people register to your board. This can be seen as an anti-spam built in feature.";
$l['setting_myfbconnect_passwordpm'] = "Send PM upon registration";
$l['setting_myfbconnect_passwordpm_desc'] = "If this option is enabled, the user will be notified with a PM telling his randomly generated password upon his registration.";
$l['setting_myfbconnect_passwordpm_subject'] = "PM subject";
$l['setting_myfbconnect_passwordpm_subject_desc'] = "Choose a default subject to use in the generated PM.";
$l['setting_myfbconnect_passwordpm_message'] = "PM message";
  $l['setting_myfbconnect_passwordpm_message_desc'] = "Write down a default message which will be sent to the registered users when they register with Facebook. {user} and {password} are variables and refer to the username the former and the randomly generated password the latter: they should be there even if you modify the default message. HTML and BBCode are permitted here.";
$l['setting_myfbconnect_passwordpm_fromid'] = "PM sender";
$l['setting_myfbconnect_passwordpm_fromid_desc'] = "Insert the UID of the user who will be the sender of the PM. By default it is set to 0 which is MyBB Engine, but you can change it to whatever you like.";
$l['setting_myfbconnect_postonwall'] = "Post on user's wall";
$l['setting_myfbconnect_postonwall_desc'] = "Enable this option to post a message on the user's wall when he registers or links his account to your board. Posting permissions will be automatically asked when authorizing your application. When this is active, users might wait a bit more when registering for the first time due to data being transferred to Facebook.";
$l['setting_myfbconnect_postonwall_message'] = "Custom post message";
$l['setting_myfbconnect_postonwall_message_desc'] = "Enter a custom post which will be posted to the user's wall. You can use {bbname} and {bburl} to refer to your board's name and your board's URL.";

// Custom fields
$l['setting_myfbconnect_fbavatar'] = "Sync avatar and cover";
$l['setting_myfbconnect_fbavatar_desc'] = "If you would like to import avatar and cover from Facebook (and let users decide to sync them) enable this option.";
$l['setting_myfbconnect_fbbday'] = "Sync birthday";
$l['setting_myfbconnect_fbbday_desc'] = "If you would like to import birthday from Facebook (and let users decide to sync it) enable this option.";
$l['setting_myfbconnect_fblocation'] = "Sync location";
$l['setting_myfbconnect_fblocation_desc'] = "If you would like to import Location from Facebook (and let users decide to sync it) enable this option.";
$l['setting_myfbconnect_fblocationfield'] = "Location Custom Profile Field";
$l['setting_myfbconnect_fblocationfield_desc'] = "Select the Custom Profile Field that will be filled with Facebook's location.";
$l['setting_myfbconnect_fbbio'] = "Sync biography";
$l['setting_myfbconnect_fbbio_desc'] = "If you would like to import Biography from Facebook (and let users decide to sync it) enable this option.";
$l['setting_myfbconnect_fbbiofield'] = "Biography Custom Profile Field";
$l['setting_myfbconnect_fbbiofield_desc'] = "Select the Custom Profile Field that will be filled with Facebook's biography.";
$l['setting_myfbconnect_fbdetails'] = "Sync first and last name";
$l['setting_myfbconnect_fbdetails_desc'] = "If you would like to import first and last name from Facebook (and let users decide to sync it) enable this option.";
$l['setting_myfbconnect_fbdetailsfield'] = "First and last name Custom Profile Field";
$l['setting_myfbconnect_fbdetailsfield_desc'] = "Select the Custom Profile Field that will be filled with Facebook's first and last name.";
$l['setting_myfbconnect_fbsex'] = "Sync sex";
$l['setting_myfbconnect_fbsex_desc'] = "If you would like to import sex from Facebook (and let users decide to sync it) enable this option.";
$l['setting_myfbconnect_fbsexfield'] = "Sex Custom Profile Field";
$l['setting_myfbconnect_fbsexfield_desc'] = "Select the Custom Profile Field that will be filled with Facebook's sex.";

// Default pm text
$l['myfbconnect_default_passwordpm_subject'] = "New password";
$l['myfbconnect_default_passwordpm_message'] = "Welcome on our Forums, dear {user}!

We appreciate that you have registered with Facebook. We have generated a random password for you which you should take note somewhere if you would like to change your personal infos. We require for security reasons that you specify your password when you change things such as the email, your username and the password itself, so keep it secret!

Your password is: [b]{password}[/b]

With regards,
our Team";
$l['myfbconnect_default_postonwall_message'] = "I have just registered on #{bbname}! Join me now registering with Facebook on {bburl}!";

// Errors
$l['myfbconnect_error_needtoupdate'] = "You seem to have currently installed an outdated version of MyFacebook Connect. Please <a href=\"index.php?module=config-settings&update=myfbconnect\">click here</a> to run the upgrade script.";
$l['myfbconnect_error_nothingtodohere'] = "Ooops, MyFacebook Connect is already up-to-date! Nothing to do here...";

// Success
$l['myfbconnect_success_updated'] = "MyFacebook Connect has been updated correctly from version {1} to {2}. Good job!";

// ACP Module
$l['myfbconnect_file_status'] = "File Status";
$l['myfbconnect_file'] = "File";
$l['myfbconnect_status'] = "Status";
$l['myfbconnect_general'] = "General";
$l['myfbconnect_general_desc'] = "Check the file status of MyFacebook Connect, common troubleshooting routines and other stuff.";
$l['myfbconnect_status_ok'] = "All MyFacebook Connect files are present in the correct directories.";
$l['myfbconnect_status_notok'] = "Some files are missing. Please add them as soon as possible.";
$l['myfbconnect_status_notok_harm'] = "Some files are missing, and some of them are critical for MyFacebook Connect's work. Please add them as soon as possible.";
$l['myfbconnect_settings'] = "Settings";

// Others
$l['myfbconnect_select_nofieldsavailable'] = "<span style='color: red'>There are no profile fields available. <b><a href='index.php?module=config-profile_fields'>Create one</a></b> to use this functionality.</span>";