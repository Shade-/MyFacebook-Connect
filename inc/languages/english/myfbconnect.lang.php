<?php

$l['myfbconnect'] = "MyFacebook Connect";
$l['myfbconnect_login'] = "Login with Facebook";

// Redirects
$l['myfbconnect_redirect_logged_in'] = "You have successfully logged in with Facebook.";
$l['myfbconnect_redirect_registered'] = "You have successfully registered and logged in with Facebook.";
$l['myfbconnect_redirect_title'] = "Welcome, {1}!";

// Errors
$l['myfbconnect_error_no_config_found'] = "You haven't configured MyFacebook Connect plugin yet: either your Facebook Application ID or your Facebook Application Secret are missing. If you are an administrator, please read the instructions provided in the documentation.";
$l['myfbconnect_error_report'] = "An unknown error occurred, but a bug report has <b>not</b> been generated. Here is the output:<br>
<pre>{1}</pre><br>
Please report this error to an administrator and try again.";
$l['myfbconnect_error_report_generated'] = "The following error occurred trying to accomplish your request:<br>
<pre>{1}</pre>{2}";
$l['myfbconnect_error_report_generated_user'] = "<br>Get in touch with an administrator as soon as possible.";
$l['myfbconnect_error_report_generated_admin'] = "<br>A complete bug report has been generated and it's available in your administration panel under MyFacebook Connect settings page.";
$l['myfbconnect_error_verified_only'] = "Only verified Facebook accounts are allowed to register or login. Please verify your Facebook account before tempting to register or login here again.";
$l['myfbconnect_error_no_id_provided'] = "An unknown error occurred while fetching your data from Facebook. Either the plugin was not configured properly or this server doesn't support server-to-server connections. Please report this error to an administrator.";
$l['myfbconnect_error_missing_access_token'] = "The access token is missing. You probably didn't authorize our application to gather your data. Approving our application is mandatory if you want to log in or register to our Forums, please retry and authorize our application when asked. If you authorized the application but you still cannot log in or register, please contact an administrator.";
$l['myfbconnect_error_unknown'] = "An unknown error occurred.";

// UserCP
$l['myfbconnect_settings_title'] = $l['myfbconnect_page_title'] = "Facebook integration";
$l['myfbconnect_settings_save'] = "Save";
$l['myfbconnect_settings_unlink'] = "Unlink my account";
$l['myfbconnect_settings_fbavatar'] = "Avatar and cover";
$l['myfbconnect_settings_fbsex'] = "Sex";
$l['myfbconnect_settings_fbbio'] = "Bio";
$l['myfbconnect_settings_fbdetails'] = "Name and last name";
$l['myfbconnect_settings_fbbday'] = "Birthday";
$l['myfbconnect_settings_fblocation'] = "Location";
$l['myfbconnect_link'] = "Connect to Facebook";
$l['myfbconnect_settings_what_to_sync'] = "Choose what informations we should import from your Facebook account every time you log in. Informations are only added, if you want to remove something you must change your profile from <a href='usercp.php?action=profile'>this page</a>.";
$l['myfbconnect_settings_link_account'] = "Link your account to Facebook in order to log in easier and faster next time you visit us.";
$l['myfbconnect_settings_connected'] = "Your Facebook account is currently linked to the account on this board. Click on the button below to unlink.";

// Registration
$l['myfbconnect_register_title'] = "Facebook registration";
$l['myfbconnect_register_basic_info'] = "Choose an username, an email and a password to register your account. Your Facebook account identifier ({1}) will be hashed, stored and used for your future authentication in order to let you log in quickly and seamlessly.";
$l['myfbconnect_register_what_to_sync'] = "Choose what informations we should import from your Facebook account every time you log in.";
$l['myfbconnect_register_username'] = "Username:";
$l['myfbconnect_register_email'] = "Email:";
$l['myfbconnect_register_button'] = "Register";

// Success Messages
$l['myfbconnect_success_account_linked'] = "Your account on this board has been correctly linked to your Facebook's one.";
$l['myfbconnect_success_account_linked_title'] = "Account linked";
$l['myfbconnect_success_account_unlinked'] = "Your Facebook account has been unlinked successfully from your MyBB's one.";
$l['myfbconnect_success_account_unlinked_title'] = "Account unlinked";
$l['myfbconnect_success_settings_updated'] = "Your Facebook integration related settings have been updated correctly.";
$l['myfbconnect_success_settings_updated_title'] = "Settings updated";

// Who's Online
$l['myfbconnect_viewing_loggingin'] = "<a href=\"myfbconnect.php?action=fblogin\">Logging in with Facebook</a>";
$l['myfbconnect_viewing_registering'] = "<a href=\"myfbconnect.php?action=fbregister\">Registering with Facebook</a>";

// Misc
$l['myfbconnect_male'] = "Male";
$l['myfbconnect_female'] = "Female";