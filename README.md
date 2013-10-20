MyFacebook Connect
===============================

> **Current version** 1.1.1  
> **Dependencies** [PluginLibrary][1], cURL installed and running on your server  
> **Author** Shade  

General
-------

MyFacebook Connect is meant to be the missing bridge between Facebook and MyBB. It lets your users login with their Facebook account, registering if they don't have an account on your board, and linking their Facebook account to their account on your board if they have one already.

The plugin adds 21 settings into your Admin Control Panel which let you specify the Facebook App ID, Facebook App Secret, the post-registration usergroup the user will be inserted when registering through Facebook, whether to use fast one-click registrations and other minor settings.

MyFacebook Connect currently comes with the following feature list:

* Connect any user to your MyBB installation with Facebook
* One-click login
* One-click registration if setting "Fast registration" is enabled, else the user will be asked for a new username, a new email and data syncing permissions
* Automatically synchronizes Facebook account data with MyBB account, including avatar, birthday, cover (if Profile Pictures plugin is installed), location, biography, first and last name and sex
* If an user has already a registered account, logging in with Facebook will cause the system to attempt linking his already-registered account with his Facebook, processing the email. If the email check returns false, a new account will be registered
* Already-registered users can link to their Facebook account manually from within their User Control Panel
* Facebook-linked users can choose what data to import from their Facebook from within their User Control Panel
* Admins have full control over the allowed-to-be-imported data
* Facebook-linked users can decide at any time to unlink their account from your board hitting a button
* Works for all MyBB 1.6 installations and web servers thanks to the Facebook SDK provided. It requires your server to have cURL installed and running properly with HTTPS support, which it's somewhat the standard configuration and shouldn't cause any problems
* You can set a post-registration usergroup to insert the Facebook-registered users, meaning a smoother user experience
* You can notify a newly registered user with a PM containing his randomly generated password. You have full control on the subject, the sender and the message of the PM that you can edit from your Admin Control Panel
* You have full control over synchronized data. You can choose what data to let your users sync with their Facebook accounts by simply enabling the settings into the Admin Control Panel
* You can decide whether to restrict registration to verified-only Facebook users to prevent spam and bots registering to your board
* Redirects logged in/registered users to the same page they came from
* **It works**
* **It's free**

Documentation
-------------

Please refer to the official documentation [here][2].

Future updates
-------------

Would you like to see a feature developed for MyFacebook Connect? No problem, just open a new Issue here on GitHub and I'll do my best to accomplish your request!

It is based upon Facebook SDK 3.x. It is free as in freedom.

[1]: http://mods.mybb.com/view/PluginLibrary
[2]: http://github.com/Shade-/MyFacebook-Connect/wiki
