<?php
// Installation
$l['myfbconnect'] = "MyFacebook Connect";
$l['myfbconnect_pluginlibrary_missing'] = "<a href=\"http://mods.mybb.com/view/pluginlibrary\">PluginLibrary</a> is missing. Please install it before doing anything else with MyFacebook Connect.";

// Settings
$l['setting_group_myfbconnect'] = "Facebook Login and Registration";
$l['setting_group_myfbconnect_desc'] = "Here you can manage Facebook login and registration on your board, changing API keys and options to enable or disable certain aspects of MyFacebook Connect plugin.";
$l['setting_myfbconnect_enabled'] = "Master switch";
$l['setting_myfbconnect_enabled_desc'] = "Do you want to let your users login and register with Facebook? If an user is already registered the account will be linked to its Facebook account.";
$l['setting_myfbconnect_appid'] = "App ID";
$l['setting_myfbconnect_appid_desc'] = "Enter your App ID token from Facebook Developers site. This will be used together with the secret token to ask authorizations to your users through your app.";
$l['setting_myfbconnect_appsecret'] = "App Secret";
$l['setting_myfbconnect_appsecret_desc'] = "Enter your App Secret token from Facebook Developers site. This will be used together with the ID token to ask authorizations to your users through your app.";
$l['setting_myfbconnect_fastregistration'] = "One-click registration";
$l['setting_myfbconnect_fastregistration_desc'] = "If this option is disabled, when an user wants to register with Facebook he will be asked for permissions for your app if it's the first time he is logging in, otherwise he will be registered and logged in immediately without asking for username changes and what data to sync.";
$l['setting_myfbconnect_usergroup'] = "After registration usergroup";
$l['setting_myfbconnect_usergroup_desc'] = "Select the after-registration usergroup. The user will be inserted directly into this usergroup upon registering. Also, if an existing user links his account to Facebook, this usergroup will be added to his additional groups list.";
$l['setting_myfbconnect_use_secondary'] = "Apply usergroup to existing users' secondary list";
$l['setting_myfbconnect_use_secondary_desc'] = "If this option is enabled, existing users who link their account to Facebook will be granted the above usergroup within their secondary usergroups list. Otherwise, they will not be getting any usergroup upgrade.";
$l['setting_myfbconnect_keeprunning'] = "Force operational status";
$l['setting_myfbconnect_keeprunning_desc'] = "Enable this option to let MyFacebook Connect run even if registrations are disabled. This is particularly useful if you want to allow new registrations only with Facebook.";
$l['setting_myfbconnect_scopes'] = "Scopes (advanced)";
$l['setting_myfbconnect_scopes_desc'] = "Add the scopes (aka permissions) you want to ask for by your app. <b>Edit only if you know what a scope is and you know what you are doing.</b> Example: to request the user's location, add user_location. Note that at the moment, only location is supported. You will need to send your app for review to use this permission. A complete list is available here: https://developers.facebook.com/docs/permissions/reference";
$l['setting_myfbconnect_passwordpm'] = "Send PM upon registration";
$l['setting_myfbconnect_passwordpm_desc'] = "If this option is enabled, the user will be notified with a PM telling his randomly generated password upon his registration.";
$l['setting_myfbconnect_passwordpm_subject'] = "PM subject";
$l['setting_myfbconnect_passwordpm_subject_desc'] = "Choose a default subject to use in the generated PM.";
$l['setting_myfbconnect_passwordpm_message'] = "PM message";
  $l['setting_myfbconnect_passwordpm_message_desc'] = "Write down a default message which will be sent to the registered users when they register with Facebook. {user} and {password} are variables and refer to the username the former and the randomly generated password the latter: they should be there even if you modify the default message. HTML and BBCode are permitted here.";
$l['setting_myfbconnect_passwordpm_fromid'] = "PM sender";
$l['setting_myfbconnect_passwordpm_fromid_desc'] = "Insert the UID of the user who will be the sender of the PM. By default it is set to 0 which is MyBB Engine, but you can change it to whatever you like.";

// Custom fields
$l['setting_myfbconnect_fbavatar'] = "Sync avatar and cover";
$l['setting_myfbconnect_fbavatar_desc'] = "If you would like to import avatar and cover from Facebook (and let users decide to sync them) enable this option.";
$l['setting_myfbconnect_fbbday'] = "Sync birthday";
$l['setting_myfbconnect_fbbday_desc'] = "If you would like to import birthday from Facebook (and let users decide to sync it) enable this option.";
$l['setting_myfbconnect_fblocation'] = "Sync location";
$l['setting_myfbconnect_fblocation_desc'] = "If you would like to import Location from Facebook (and let users decide to sync it) enable this option.";
$l['setting_myfbconnect_fblocationfield'] = "Location Custom Profile Field";
$l['setting_myfbconnect_fblocationfield_desc'] = "Select the Custom Profile Field that will be filled with Facebook's location.";
$l['setting_myfbconnect_fbdetails'] = "Sync first and last name";
$l['setting_myfbconnect_fbdetails_desc'] = "If you would like to import first and last name from Facebook (and let users decide to sync it) enable this option.";
$l['setting_myfbconnect_fbdetailsfield'] = "First and last name Custom Profile Field";
$l['setting_myfbconnect_fbdetailsfield_desc'] = "Select the Custom Profile Field that will be filled with Facebook's first and last name.";
$l['setting_myfbconnect_fbsex'] = "Sync sex";
$l['setting_myfbconnect_fbsex_desc'] = "If you would like to import sex from Facebook (and let users decide to sync it) enable this option.";
$l['setting_myfbconnect_fbsexfield'] = "Sex Custom Profile Field";
$l['setting_myfbconnect_fbsexfield_desc'] = "Select the Custom Profile Field that will be filled with Facebook's sex.";

// Default PM
$l['myfbconnect_default_passwordpm_subject'] = "New password";
$l['myfbconnect_default_passwordpm_message'] = "Welcome on our Forums, dear {user}!

We appreciate that you have registered with Facebook. We have generated a random password for you which you should take note somewhere if you would like to change your personal infos. We require for security reasons that you specify your password when you change things such as the email, your username and the password itself, so keep it secret!

Your password is: [b]{password}[/b]

With regards,
our Team";
$l['myfbconnect_default_postonwall_message'] = "I have just registered on #{bbname}! Join me now registering with Facebook on {bburl}!";

// Bug reports
$l['myfbconnect_reports'] = "Bug reports";
$l['myfbconnect_reports_date'] = "Date";
$l['myfbconnect_reports_line'] = "Line";
$l['myfbconnect_reports_file'] = "File";
$l['myfbconnect_reports_code'] = "Code";
$l['myfbconnect_reports_export'] = "Export";
$l['myfbconnect_reports_delete'] = "Delete";
$l['myfbconnect_reports_delete_all'] = "Delete all reports";

// Errors
$l['myfbconnect_error_needtoupdate'] = "You seem to have currently installed an outdated version of MyFacebook Connect. Please <a href=\"index.php?module=config-settings&update=myfbconnect\">click here</a> to run the upgrade script.";
$l['myfbconnect_error_nothingtodohere'] = "Ooops, MyFacebook Connect is already up-to-date! Nothing to do here...";
$l['myfbconnect_error_port_443_not_open'] = "A connection test has been made, and your server's 443 port seems to be closed. Facebook needs port 443 open to communicate and authenticate users. This test might fail under certain circumstances: <b>you can still install the plugin and give it a try by clicking <a href='index.php?module=config-plugins&action=activate&plugin=myfbconnect&skip_port_check=true&my_post_key={1}'>here</a></b>, but keep in mind that the connection test has returned an invalid response. If:<br /><br />
<li>you are running on a <b>dedicated or premium hosting</b>, you most probably have access to a port manager or something similar. You can easily open 443 port on TCP protocol by accessing the manager.</li>
<li>you are running on a <b>shared hosting</b>, or you don't have access to a port manager, you must contact your host and ask for port 443 to be opened for you. This is the only way to let your users login and register with Facebook.</li>
The installation has been aborted for security reasons.";

// Success
$l['myfbconnect_success_updated'] = "MyFacebook Connect has been updated correctly from version {1} to {2}. Good job!";
$l['myfbconnect_success_deleted_reports'] = "The bug report(s) has been deleted successfully.";

// Others
$l['myfbconnect_select_nofieldsavailable'] = "<span style='color: red'>There are no profile fields available. <b><a href='index.php?module=config-profile_fields'>Create one</a></b> to use this functionality.</span>";
