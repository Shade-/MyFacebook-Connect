<?php

/**
 * A bridge between MyBB with Facebook, featuring login, registration and more.
 *
 * @package Main API class
 * @version 2.0
 */
 
class MyFacebook
{
	// The fallback URL where Facebook redirects users
	private $fallback;
	
	// The $facebook object populated upon initialization
	public $facebook;
	
	/**
	 * Contructor
	 */
	public function __construct()
	{
		global $mybb, $lang;
		
		if (!$lang->myfbconnect) {
			$lang->load('myfbconnect');
		}
		
		$this->load_api();
		$this->set_fallback();
	}
	
	/**
	 * Loads the necessary API classes
	 */
	private function load_api()
	{
		global $mybb, $lang;
		
		if ($this->facebook) {
			return false;
		}
		
		try {
			require_once MYBB_ROOT . "myfbconnect/src/facebook.php";
		}
		catch (Exception $e) {
			error($lang->sprintf($lang->myfbconnect_error_report, $e->getMessage()));
		}
		
		if (!$mybb->settings['myfbconnect_appid'] or !$mybb->settings['myfbconnect_appsecret']) {
			error($lang->myfbconnect_error_noconfigfound);
		}
		
		// Create our application instance
		$this->facebook = new Facebook(array(
			'appId' => $mybb->settings['myfbconnect_appid'],
			'secret' => $mybb->settings['myfbconnect_appsecret']
		));
		
		return true;
	}
	
	/**
	 * Sets the fallback URL where the app should redirect to when finished authenticating
	 */
	public function set_fallback($url = '')
	{
		global $mybb;
		
		if (!$url) {
			$this->fallback = $mybb->settings['bburl'] . "/myfbconnect.php?action=do_login";
		}
		else {
			$this->fallback = $mybb->settings['bburl'] . "/" . $url;
		}
		
		return true;
	}
	
	/**
	 * Starts the login process, creating the authorize URL
	 */
	public function authenticate()
	{
		global $mybb;
		
		$permissions = array(
			'user_birthday',
			'user_location',
			'email'
		);
		if ($mybb->settings['myfbconnect_requestpublishingperms']) {
			$permissions[] = 'publish_stream';
		}
		
		// Get the URL and redirect the user
		$redirect = $this->facebook->getLoginUrl(array(
			'scope' => implode(',', $permissions),
			'redirect_uri' => $this->fallback
		));
		
		header("Location: " . $redirect);
		
		return true;
	}
	
	/**
	 * Attempts to get the authenticated user's data
	 */
	public function get_user($fields = '')
	{
		global $lang;
		
		if (!$fields) {
			$fields = "id,name,email,cover,birthday,website,gender,bio,location,verified";
		}
		
		try {
			$user = $this->facebook->api('/me?fields=' . $fields);
		}
		catch (FacebookApiException $e) {
			// The user should have denied permissions. Still available with check_user() though.
			error($lang->sprintf($lang->myfbconnect_error_report, $e->getMessage()));
		}
		
		if ($user) {
			return $user;
		}
		
		return false;
	}
	
	/**
	 * Checks if the authenticated user is available
	 */
	public function check_user()
	{
		global $lang;
		
		if ($this->facebook->getUser()) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Logins an user by adding a cookie into his browser and updating his session
	 */
	public function login($user = '')
	{
		global $mybb, $session, $db;
		
		if (!$user) {
			$user = $mybb->user;
		}
		
		if (!$user['uid'] or !$user['loginkey'] or !$session) {
			return false;
		}
		
		// Delete all the old sessions
		$db->delete_query("sessions", "ip='" . $db->escape_string($session->ipaddress) . "' AND sid != '" . $session->sid . "'");
		
		// Create a new session
		$db->update_query("sessions", array(
			"uid" => $user['uid']
		), "sid='" . $session->sid . "'");
		
		// Set up the login cookies
		my_setcookie("mybbuser", $user['uid'] . "_" . $user['loginkey'], null, true);
		my_setcookie("sid", $session->sid, -1, true);
		
		return true;
	}
	
	/**
	 * Registers an user with Facebook data
	 */
	public function register($user)
	{
		if (!$user) {
			return false;
		}
		
		global $mybb, $session, $plugins, $lang;
		
		require_once MYBB_ROOT . "inc/datahandlers/user.php";
		$userhandler = new UserDataHandler("insert");
		
		$plength = 8;
		if ($mybb->settings['minpasswordlength']) {
			$plength = (int) $mybb->settings['minpasswordlength'];
		}
		
		$password = random_str($plength);
		
		$new_user = array(
			"username" => $user['name'],
			"password" => $password,
			"password2" => $password,
			"email" => $user['email'],
			"email2" => $user['email'],
			"usergroup" => $mybb->settings['myfbconnect_usergroup'],
			"displaygroup" => $mybb->settings['myfbconnect_usergroup'],
			"regip" => $session->ipaddress,
			"longregip" => my_ip2long($session->ipaddress),
			"options" => array(
				"hideemail" => 1
			)
		);
		
		/* Registration might fail for custom profile fields required at registration... workaround = IN_ADMINCP defined.
		Placed straight before the registration process to avoid conflicts with third party plugins messying around with
		templates (I'm looking at you, PHPTPL) */
		define("IN_ADMINCP", 1);
		
		$userhandler->set_data($new_user);
		if ($userhandler->validate_user()) {
			
			$user = $userhandler->insert_user();
			
			$plugins->run_hooks("member_do_register_end");
			
			// Deliver a welcome PM
			if ($mybb->settings['myfbconnect_passwordpm']) {
				
				require_once MYBB_ROOT . "inc/datahandlers/pm.php";
				$pmhandler                 = new PMDataHandler();
				$pmhandler->admin_override = true;
				
				// Make sure admins haven't done something bad
				$fromid = (int) $mybb->settings['myfbconnect_passwordpm_fromid'];
				if (!$mybb->settings['myfbconnect_passwordpm_fromid'] OR !user_exists($mybb->settings['myfbconnect_passwordpm_fromid'])) {
					$fromid = 0;
				}
				
				$message = $mybb->settings['myfbconnect_passwordpm_message'];
				$subject = $mybb->settings['myfbconnect_passwordpm_subject'];
				
				$thingsToReplace = array(
					"{user}" => $user['username'],
					"{password}" => $password
				);
				
				// Replace what needs to be replaced
				foreach ($thingsToReplace as $find => $replace) {
					$message = str_replace($find, $replace, $message);
				}
				
				$pm = array(
					"subject" => $subject,
					"message" => $message,
					"fromid" => $fromid,
					"toid" => array(
						$user['uid']
					)
				);
				
				// Some defaults :)
				$pm['options'] = array(
					"signature" => 1
				);
				
				$pmhandler->set_data($pm);
				
				// Now let the PM handler do all the hard work
				if ($pmhandler->validate_pm()) {
					$pmhandler->insert_pm();
				}
				else {
					error($lang->sprintf($lang->myfbconnect_error_report, $pmhandler->get_friendly_errors()));
				}
			}
			
			// Finally return our new user data
			return $user;
			
		}
		else {
			return array(
				'error' => $userhandler->get_friendly_errors()
			);
		}
		
		return true;
	}
	
	/**
	 * Links an user with Facebook
	 */
	public function link_user($user = '', $id)
	{
		global $mybb, $db;
		
		if (!$id) {
			return false;
		}
		
		if (!$user) {
			$user = $mybb->user;
		}
		
		// Still no user?
		if (!$user) {
			return false;
		}
		
		$update = array(
			"myfb_uid" => (int) $id
		);
		
		$db->update_query("users", $update, "uid = {$user['uid']}");
		
		// Add to the usergroup
		if ($mybb->settings['myfbconnect_usergroup']) {
			$this->join_usergroup($user, $mybb->settings['myfbconnect_usergroup']);
		}
		
		return true;
	}
	
	/**
	 * Unlinks an user from Facebook
	 */
	public function unlink_user($user = '')
	{
		global $mybb, $db;
		
		if (!$user) {
			$user = $mybb->user;
		}
		
		// Still no user?
		if (!$user) {
			return false;
		}
		
		$update = array(
			"myfb_uid" => 0
		);
		
		$db->update_query("users", $update, "uid = {$user['uid']}");
		
		// Remove from the usergroup
		if ($mybb->settings['myfbconnect_usergroup']) {
			$this->leave_usergroup($user, $mybb->settings['myfbconnect_usergroup']);
		}
		
		return true;
	}
	
	/**
	 * Processes an user
	 */
	public function process($user)
	{
		global $mybb, $db, $session, $lang;
		
		// Just verified allowed?
		if ($mybb->settings['myfbconnect_verifiedonly']) {
			
			if ($user['verified'] === false) {
				error($lang->myfbconnect_error_verifiedonly);
			}
			
		}
		
		if (!$user['id']) {
			error($lang->myfbconnect_error_noidprovided);
		}
		
		if ($user['email']) {
			$sql = " OR email = '" . $db->escape_string($user['email']) . "'";
		}
		
		// Let's see if you are already with us
		$query   = $db->simple_select("users", "*", "myfb_uid = {$user['id']}{$sql}", array(
			"limit" => 1
		));
		$account = $db->fetch_array($query);
		
		$message = $lang->myfbconnect_redirect_loggedin;
		
		// Decide what to do
		if ($user['email'] and $account['email'] == $user['email'] and !$account['myfb_uid']) {
			
			// Link + login
			$this->link_user($account);
			$this->login($account);
			
		}
		else if ($account['myfb_uid']) {
			
			// Login
			$this->login($account);
			
		}
		else if (!$account) {
			
			// Register + login
			if (!$mybb->settings['myfbconnect_fastregistration']) {
				header("Location: myfbconnect.php?action=register");
				return false;
			}
			global $plugins;
			$account = $this->register($user);
			
			if ($account['error']) {
				return $account;
			}
			
			$this->login($account);
			
			$message = $lang->myfbconnect_redirect_registered;
			
		}
		
		$title = $lang->sprintf($lang->myfbconnect_redirect_title, $account['username']);
		
		$this->redirect('', $title, $message);
	}
	
	/**
	 * Adds the logged in user to an additional group without losing the existing values
	 */
	public function join_usergroup($user, $gid)
	{
		global $mybb, $db;
		
		if (!$gid) {
			return false;
		}
		
		if (!$user) {
			$user = $mybb->user;
		}
		
		if (!$user) {
			return false;
		}
		
		$gid = (int) $gid;
		
		// Is this user already in that group?
		if ($user['usergroup'] == $gid) {
			return false;
		}
		
		$groups = explode(",", $user['additionalgroups']);
		
		if (!in_array($gid, $groups)) {
			
			$groups[] = $gid;
			$update   = array(
				"additionalgroups" => implode(",", array_filter($groups))
			);
			$db->update_query("users", $update, "uid = {$user['uid']}");
			
		}
		
		return true;
	}
	
	/**
	 * Removes the logged in user from an additional group without losing the existing values
	 */
	public function leave_usergroup($user, $gid)
	{
		global $mybb, $db;
		
		if (!$gid) {
			return false;
		}
		
		if (!$user) {
			$user = $mybb->user;
		}
		
		if (!$user) {
			return false;
		}
		
		$gid = (int) $gid;
		
		// Is this user already in that group?
		if ($user['usergroup'] == $gid) {
			return false;
		}
		
		$groups = explode(",", $user['additionalgroups']);
		
		if (in_array($gid, $groups)) {
			
			// Flip the array so we have gid => keys
			$groups = array_flip($groups);
			unset($groups[$gid]);
			
			// Restore the array flipping it again (and filtering it)
			$groups = array_filter(array_flip($groups));
			
			$update = array(
				"additionalgroups" => implode(",", $groups)
			);
			$db->update_query("users", $update, "uid = {$user['uid']}");
			
		}
		
		return true;
	}
	
	/**
	 * Redirects the user to the page he came from
	 */
	public function redirect($url = '', $title = '', $message = '')
	{
		if (!$url) {
			$url = $_SERVER['HTTP_REFERER'];
		}
		
		if (!strpos($url, "action=login") and !strpos($url, "action=do_login") and !strpos($url, "action=register")) {
			$url = htmlspecialchars_uni($url);
		}
		else {
			$url = "index.php";
		}
		
		redirect($url, $message, $title);
		
		return true;
	}
	
	/**
	 * Debugs any type of data, printing out an array and immediately killing the execution of the currently running script
	 */
	public function debug($data)
	{
		// Fallback for arrays
		if (is_array($data)) {
			$data = array_map('htmlspecialchars_uni', $data);
		}
		// Fallback for strings
		else if (is_string($data)) {
			$data = htmlspecialchars_uni($data);
		}
		
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		
		exit;
	}
}