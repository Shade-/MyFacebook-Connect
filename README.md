MyFacebook Connect
===============================

> **Current version** beta 1  
> **Dependencies** [PluginLibrary][1]  
> **Author** Shade  

General
-------

MyFacebook Connect is meant to be the missing bridge between Facebook and MyBB. The plan is to let users login with their Facebook account, registering if they don't have an account on your board, and linking their Facebook account to their account on your board if they have one already. **At the moment MyFacebook Connect is in development stages but is sufficiently stable to be used at least on a test board**.

The plugin adds 4 settings into your Admin Control Panel which let you specify the Facebook App ID, Facebook App Secret and the post-registration usergroup the user will be inserted when registering through Facebook.

MyFacebook Connect currently comes with the following feature list:

* Connect any user to your MyBB installation with Facebook
* One-click login/registration
* Automatically synchronizes Facebook account data with MyBB account, including avatar, birthday and cover (if Profile Pictures plugin is installed)
* Works for all MyBB 1.6 installations and web servers thanks to the Facebook SDK provided. It requires your server to be able to store cookies, but MyBB also requires this feature so if you can run MyBB you can run MyFacebook Connect

Known issues
------------

There is currently 1 known minor issue awaiting a permanent patch:

* If the username already exists, the $userhandler->validate_user() function is returning false upon registration which isn't performed. **Temporary patched with an error output, should be fixed with a page which let the user choose another username**

Future updates
-------------

The plan for future updates includes:

* Fixes for all known issues listed above
* The chance for existing users to synchronize their accounts with their Facebook's one, merging missing data and overwriting old data if specified
* Redirect to the same page the user came from when he clicked on Login with Facebook link
* Notify a newly registered user with either a PM, an email or a MyAlerts alert (if MyAlerts is installed) containing his randomly generated password
* And more!

It is based upon Facebook SDK 3.x. It is free as in freedom.

[1]: http://mods.mybb.com/view/PluginLibrary
