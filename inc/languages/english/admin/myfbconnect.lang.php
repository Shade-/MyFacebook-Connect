<?php
// installation
$l['myfbconnect'] = "MyFacebook Connect";
$l['myfbconnect_pluginlibrary_missing'] = "<a href=\"http://mods.mybb.com/view/pluginlibrary\">PluginLibrary</a> is missing. Please install it before doing anything else with myfbconnect.";

// settings
$l['myfbconnect_settings'] = "Facebook login and registration settings";
$l['myfbconnect_settings_desc'] = "Here you can manage Facebook login and registration on your board, changing API keys and options to enable or disable certain aspects of MyFacebook Connect plugin.";
$l['myfbconnect_settings_enable'] = "Master switch";
$l['myfbconnect_settings_enable_desc'] = "Do you want to let your users login and register with Facebook? If an user is already registered the account will be linked to its Facebook account.";
$l['myfbconnect_settings_appid'] = "App ID";
$l['myfbconnect_settings_appid_desc'] = "Enter your App ID token from Facebook Developers site. This will be used together with the secret token to ask authorizations to your users through your app.";
$l['myfbconnect_settings_appsecret'] = "App Secret";
$l['myfbconnect_settings_appsecret_desc'] = "Enter your App Secret token from Facebook Developers site. This will be used together with the ID token to ask authorizations to your users through your app.";
$l['myfbconnect_settings_fastregistration'] = "One-click registration";
$l['myfbconnect_settings_fastregistration_desc'] = "If this option is disabled, when an user wants to register with Facebook he will be asked for permissions for your app if it's the first time he is loggin in, else he will be registered and logged in immediately without asking for username changes and what data to sync.";
$l['myfbconnect_settings_usergroup'] = "After registration usergroup";
$l['myfbconnect_settings_usergroup_desc'] = "Enter the usergroup ID you want the new users to be when they register with Facebook. By default this value is set to 2, which equals to Registered usergroup.";
$l['myfbconnect_settings_verifiedonly'] = "Allow only verified accounts";
$l['myfbconnect_settings_verifiedonly_desc'] = "Enable this option to restrict only verified Facebook users to register with Facebook. Verified users are those who confirmed their Facebook registration either with a SMS confirmation or registering with a mobile phone, so they are real people. This option is disabled by default but should be enabled in case you want to ensure that only real people register to your board. Bots won't be able to register.";
$l['myfbconnect_settings_requestpublishingperms'] = "Request publishing permissions";
$l['myfbconnect_settings_requestpublishingperms_desc'] = "If this option is enabled, the user will be asked for extra publishing permissions for your application. <b>This option should be left disabled (as it won't do anything in particular at the moment). In the future it will be crucial to let you post something on the user's wall when he registers or logins to your board.";
$l['myfbconnect_settings_passwordpm'] = "Send PM upon registration";
$l['myfbconnect_settings_passwordpm_desc'] = "If this option is enabled, the user will be notified with a PM telling his randomly generated password upon his registration.";
$l['myfbconnect_settings_passwordpm_subject'] = "PM subject";
$l['myfbconnect_settings_passwordpm_subject_desc'] = "Choose a default subject to use in the generated PM.";
$l['myfbconnect_settings_passwordpm_message'] = "PM message";
  $l['myfbconnect_settings_passwordpm_message_desc'] = "Write down a default message which will be sent to the registered users when they register with Facebook. {user} and {password} are variables and refer to the username the former and the randomly generated password the latter: they should be there even if you modify the default message. HTML and BBCode are permitted here.";
$l['myfbconnect_settings_passwordpm_fromid'] = "PM sender";
$l['myfbconnect_settings_passwordpm_fromid_desc'] = "Insert the UID of the user who will be the sender of the PM. By default is set to 0 which is MyBB Engine, but you can change it to whatever you like.";
// custom fields support, yay!
$l['myfbconnect_settings_fbavatar'] = "Sync avatar and cover";
$l['myfbconnect_settings_fbavatar_desc'] = "If you would like to import avatar and cover from Facebook (and let users decide to sync them) enable this option.";
$l['myfbconnect_settings_fbbday'] = "Sync birthday";
$l['myfbconnect_settings_fbbday_desc'] = "If you would like to import birthday from Facebook (and let users decide to sync it) enable this option.";
$l['myfbconnect_settings_fblocation'] = "Sync location";
$l['myfbconnect_settings_fblocation_desc'] = "If you would like to import Location from Facebook (and let users decide to sync it) enable this option.";
$l['myfbconnect_settings_fblocationfield'] = "Location Custom Profile Field ID";
$l['myfbconnect_settings_fblocationfield_desc'] = "Insert the Custom Profile Field ID which corresponds to the Location field. Make sure it's the right ID while you fill it! Default to 1 (MyBB's default)";
$l['myfbconnect_settings_fbbio'] = "Sync biography";
$l['myfbconnect_settings_fbbio_desc'] = "If you would like to import Biography from Facebook (and let users decide to sync it) enable this option.";
$l['myfbconnect_settings_fbbiofield'] = "Biography Custom Profile Field ID";
$l['myfbconnect_settings_fbbiofield_desc'] = "Insert the Custom Profile Field ID which corresponds to the Biography field. Make sure it's the right ID while you fill it! Default to 2 (MyBB's default)";
$l['myfbconnect_settings_fbdetails'] = "Sync first and last name";
$l['myfbconnect_settings_fbdetails_desc'] = "If you would like to import first and last name from Facebook (and let users decide to sync it) enable this option.";
$l['myfbconnect_settings_fbdetailsfield'] = "First and last name Custom Profile Field ID";
$l['myfbconnect_settings_fbdetailsfield_desc'] = "Insert the Custom Profile Field ID which corresponds to the First and last name field. Make sure it's the right ID while you fill it! Default void (MyBB doesn't use it)";
$l['myfbconnect_settings_fbsex'] = "Sync sex";
$l['myfbconnect_settings_fbsex_desc'] = "If you would like to import sex from Facebook (and let users decide to sync it) enable this option.";
$l['myfbconnect_settings_fbsexfield'] = "Sex Custom Profile Field ID";
$l['myfbconnect_settings_fbsexfield_desc'] = "Insert the Custom Profile Field ID which corresponds to the Sex field. Make sure it's the right ID while you fill it! Default to 3 (MyBB's default)";

// default pm text
$l['myfbconnect_default_passwordpm_subject'] = "New password";
$l['myfbconnect_default_passwordpm_message'] = "Welcome on our Forums, dear {user}!

We are pleased you are registering with Facebook. We have generated a random password for you which you should take note somewhere if you would like to change your personal infos. We require for security reasons that you specify your password when you change things such as the email, your username and the password itself, so keep it secret!

Your password is: [b]{password}[/b]

With regards,
our Team";

// errors
$l['myfbconnect_error_needtoupdate'] = "You seem to have currently installed an outdated version of MyFacebook Connect. Please <a href=\"index.php?module=config-settings&upgrade=myfbconnect\">click here</a> to run the upgrade script.";
$l['myfbconnect_error_nothingtodohere'] = "Ooops, MyFacebook Connect is already up-to-date! Nothing to do here...";

// success
$l['myfbconnect_success_updated'] = "MyFacebook Connect has been updated correctly from version {1} to {2}. Good job!";