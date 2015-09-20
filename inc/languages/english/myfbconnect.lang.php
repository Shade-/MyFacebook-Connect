<?php

$l['myfbconnect'] = "MyFacebook Connect";
$l['myfbconnect_login'] = "Login with Facebook";

// redirects
$l['myfbconnect_redirect_loggedin'] = "You have successfully logged in with Facebook.";
$l['myfbconnect_redirect_registered'] = "You have successfully registered and logged in with Facebook.";
$l['myfbconnect_redirect_title'] = "Welcome, {1}!";

// errors
$l['myfbconnect_error_noconfigfound'] = "You haven't configured MyFacebook Connect plugin yet: either your Facebook Application ID or your Facebook Application Secret are missing. If you are an administrator, please read the instructions provided in the documentation.";
$l['myfbconnect_error_noauth'] = "You didn't let us login with your Facebook account. Please authorize our application from your Facebook Application manager if you would like to login into our Forum.";
$l['myfbconnect_error_report'] = "An unknown error occurred, but a bug report has <b>not</b> been generated. Here is the output:<br>
<pre>{1}</pre><br>
Please report this error to an administrator and try again.";
$l['myfbconnect_error_report_generated'] = "An error occurred trying to accomplish your request. A bug report has been generated and it's available in the administration panel. If you are not an administrator, contact one as soon as possible and tell him you have landed on this page. You may also try logging in with Facebook once again and see if it was just a temporary error. Sorry for the inconvenience.";
$l['myfbconnect_error_alreadyloggedin'] = "You are already logged into the board.";
$l['myfbconnect_error_verifiedonly'] = "Only verified Facebook accounts are allowed to register or login. Please verify your Facebook account before tempting to register or login here again.";
$l['myfbconnect_error_noidprovided'] = "An unknown error occurred while fetching your data from Facebook. Either the plugin was not configured properly or this server doesn't support server-to-server connections. Please report this error to an administrator.";
$l['myfbconnect_error_unknown'] = "An unknown error occurred.";

// usercp
$l['myfbconnect_settings_title'] = $l['myfbconnect_page_title'] = "Facebook integration";
$l['myfbconnect_settings_save'] = "Save";
$l['myfbconnect_settings_unlink'] = "Unlink my account";
$l['myfbconnect_settings_fbavatar'] = "Avatar and cover";
$l['myfbconnect_settings_fbsex'] = "Sex";
$l['myfbconnect_settings_fbbio'] = "Bio";
$l['myfbconnect_settings_fbdetails'] = "Name and last name";
$l['myfbconnect_settings_fbbday'] = "Birthday";
$l['myfbconnect_settings_fblocation'] = "Location";
$l['myfbconnect_link'] = "Click here to link your account with your Facebook's one";
$l['myfbconnect_settings_whattosync'] = "Select what info we should import from your Facebook. We'll immediately synchronize your desired data on-the-fly while updating the settings, adding what should be added (but not removing what should be removed - that's up to you!).";
$l['myfbconnect_settings_linkaccount'] = "Hit the button on your right to link your Facebook account with the one on this board.";
$l['myfbconnect_settings_connected'] = "Your Facebook account is currently linked to the account on this board. Click on the button below to unlink.";

// registration
$l['myfbconnect_register_title'] = "Facebook registration";
$l['myfbconnect_register_basicinfo'] = "Choose your basic infos on your right. They are already filled with your Facebook data, but if you want to change them you are free to do it. The account will be linked to your Facebook one immediately, automatically and regardless of your choices.";
$l['myfbconnect_register_whattosync'] = "Select what info we should import from your Facebook. We'll immediately synchronize your desired data making an exact copy of your Facebook account, dependently of your choices.";
$l['myfbconnect_register_username'] = "Username:";
$l['myfbconnect_register_email'] = "Email:";

// success messages
$l['myfbconnect_success_linked'] = "Your account on this board has been correctly linked to your Facebook's one.";
$l['myfbconnect_success_settingsupdated'] = "Your Facebook integration related settings have been updated correctly.";
$l['myfbconnect_success_settingsupdated_title'] = "Settings updated";
$l['myfbconnect_success_accunlinked'] = "Your Facebook account has been unlinked successfully from your MyBB's one.";
$l['myfbconnect_success_accunlinked_title'] = "Account unlinked";

// who's online
$l['myfbconnect_viewing_loggingin'] = "<a href=\"myfbconnect.php?action=fblogin\">Logging in with Facebook</a>";
$l['myfbconnect_viewing_registering'] = "<a href=\"myfbconnect.php?action=fbregister\">Registering with Facebook</a>";

// others
$l['myfbconnect_male'] = "Male";
$l['myfbconnect_female'] = "Female";