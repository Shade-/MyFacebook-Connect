MyFacebook Connect
===============================

> **Current version** beta 4  
> **Dependencies** [PluginLibrary][1]  
> **Author** Shade  

General
-------

MyFacebook Connect is meant to be the missing bridge between Facebook and MyBB. The plan is to let users login with their Facebook account, registering if they don't have an account on your board, and linking their Facebook account to their account on your board if they have one already. **At the moment MyFacebook Connect is in development stages but is sufficiently stable to be used at least on a test board**.

The plugin adds 7 settings into your Admin Control Panel which let you specify the Facebook App ID, Facebook App Secret, the post-registration usergroup the user will be inserted when registering through Facebook, whether to use fast one-click registrations and other minor settings.

MyFacebook Connect currently comes with the following feature list:

* Connect any user to your MyBB installation with Facebook
* One-click login
* One-click registration if setting "Fast registration" is enabled, else the user will be asked for a new username and data syncing permissions
* Automatically synchronizes Facebook account data with MyBB account, including avatar, birthday and cover (if Profile Pictures plugin is installed)
* If an user has already a registered account, logging in with Facebook will cause the system to attempt linking his already-registered account with his Facebook, basing on the email. If the email check returns false, a new account will be registered
* Already-registered users can link to their Facebook account manually from within their User Control Panel
* Facebook-linked users can choose what data to import from their Facebook from within their User Control Panel
* Works for all MyBB 1.6 installations and web servers thanks to the Facebook SDK provided. It requires your server to be able to store cookies, but MyBB also requires this feature so if you can run MyBB you can run MyFacebook Connect
* You can set a post-registration usergroup to insert the Facebook-registered users, meaning a smoother user experience

Known issues
------------

There is currently 1 known minor issue awaiting a permanent patch:

* If the username already exists, the $userhandler->validate_user() function is returning false upon registration which isn't performed. **Temporary patched with an error output, should be fixed with a page which let the user choose another username**

Future updates
-------------

The plan for future updates includes:

* Fixes for all known issues listed above
* Notify a newly registered user with either a PM, an email or a MyAlerts alert (if MyAlerts is installed) containing his randomly generated password
* And more!

It is based upon Facebook SDK 3.x. It is free as in freedom.

[1]: http://mods.mybb.com/view/PluginLibrary
