<?php

/**
 * A bridge between MyBB with Facebook, featuring login, registration and more.
 *
 * @package Main API class
 * @version 2.3
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
			$this->generate_report($e);
		}
		
		if (!$mybb->settings['myfbconnect_appid'] or !$mybb->settings['myfbconnect_appsecret']) {
			error($lang->myfbconnect_error_noconfigfound);
		}
		
		// Create our application instance
		$this->facebook = new Facebook(array(
			'appId' => $mybb->settings['myfbconnect_appid'],
			'secret' => $mybb->settings['myfbconnect_appsecret'],
			'allowSignedRequest' => false
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
			'public_profile',
			'user_about_me',
			'user_birthday',
			'user_location',
			'email'
		);
		if ($mybb->settings['myfbconnect_postonwall']) {
			$permissions[] = 'publish_actions';
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
			
			// The user should have denied permissions. Still available with check_user() though
			$this->generate_report($e);
			
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
	 * Post something on an user's wall
	 */
	private function post_on_wall($message, $link = '')
	{
		global $mybb, $lang;
		
		if (!$message) {
			return false;
		}
		
		$thingsToReplace = array(
			"{bbname}" => $mybb->settings['bbname'],
			"{bburl}" => $mybb->settings['bburl']
		);
		
		// Replace what needs to be replaced
		foreach ($thingsToReplace as $find => $replace) {
			$message = str_replace($find, $replace, $message);
		}
		
		$options = array(
			'message' => $message
		);
		
		if ($link) {
			$options['link'] = $link;
		}
		else {
			$options['link'] = $mybb->settings['bburl'];
		}
		
		try {
			$this->facebook->api('/me/feed', 'POST', $options);
		}
		catch (FacebookApiException $e) {
		
			$result = $e->getResult();
			
			if ($result['error']['code'] == 200) {
				return false;
			}
			
			// The user should have denied posting permissions, but other errors might rise.
			$this->generate_report($e);
			
		}
		
		return true;
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
		$db->delete_query("sessions", "ip='" . $db->escape_string($session->ipaddress) . "' and sid != '" . $session->sid . "'");
		
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
			"usergroup" => (int) $mybb->settings['myfbconnect_usergroup'],
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
			
			$user_info = $userhandler->insert_user();
			
			$plugins->run_hooks("member_do_register_end");
			
			// Deliver a welcome PM
			if ($mybb->settings['myfbconnect_passwordpm']) {
				
				require_once MYBB_ROOT . "inc/datahandlers/pm.php";
				$pmhandler                 = new PMDataHandler();
				$pmhandler->admin_override = true;
				
				// Make sure admins haven't done something bad
				$fromid = (int) $mybb->settings['myfbconnect_passwordpm_fromid'];
				if (!$mybb->settings['myfbconnect_passwordpm_fromid'] or !user_exists($mybb->settings['myfbconnect_passwordpm_fromid'])) {
					$fromid = 0;
				}
				
				$message = $mybb->settings['myfbconnect_passwordpm_message'];
				$subject = $mybb->settings['myfbconnect_passwordpm_subject'];
				
				$thingsToReplace = array(
					"{user}" => $user_info['username'],
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
						$user_info['uid']
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
			
			// Post a message on the user's wall
			if ($mybb->settings['myfbconnect_postonwall']) {
				$this->post_on_wall($mybb->settings['myfbconnect_postonwall_message']);
			}
			
			// Finally return our new user data
			return $user_info;
			
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
		
		// Post a message on the user's wall
		if ($mybb->settings['myfbconnect_postonwall']) {
			$this->post_on_wall($mybb->settings['myfbconnect_postonwall_message']);
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
		$db->free_result($query);
		
		$message = $lang->myfbconnect_redirect_loggedin;
		
		// Link
		if ($user['email'] and $account['email'] == $user['email'] and !$account['myfb_uid']) {
			$this->link_user($account, $user['id']);
		}
		// Register
		else if (!$account) {
			
			if (!$mybb->settings['myfbconnect_fastregistration']) {
				header("Location: myfbconnect.php?action=register");
				return false;
			}
			
			global $plugins;
			$account = $this->register($user);
			
			if ($account['error']) {
				return $account;
			}
			else {
			
				// Set some defaults
				$toCheck = array('fbavatar', 'fbbday', 'fbsex', 'fbdetails', 'fbbio', 'fblocation');
				foreach ($toCheck as $setting) {
				
					$tempKey = 'myfbconnect_' . $setting;
					$new_settings[$setting] = $mybb->settings[$tempKey];
					
				}
				
				$account = array_merge($account, $new_settings);
				
			}
			
			$message = $lang->myfbconnect_redirect_registered;
			
		}
		
		// Login
		$this->login($account);
		
		// Sync
		$this->sync($account, $user);
		
		$title = $lang->sprintf($lang->myfbconnect_redirect_title, $account['username']);
		
		// Redirect
		$this->redirect('', $title, $message);
		
		return true;
	}
	
	/**
	 * Synchronizes Facebook's data with MyBB's data
	 */
	public function sync($user, $data = '')
	{
		if (!$user['uid']) {
			return false;
		}
		
		global $mybb, $db, $session, $lang;
		
		$update         = array();
		$userfield = array();
		
		$detailsid  = "fid" . (int) $mybb->settings['myfbconnect_fbdetailsfield'];
		$locationid = "fid" . (int) $mybb->settings['myfbconnect_fblocationfield'];
		$bioid      = "fid" . (int) $mybb->settings['myfbconnect_fbbiofield'];
		$sexid      = "fid" . (int) $mybb->settings['myfbconnect_fbsexfield'];
		
		// No data available? Let's get some
		if (!$data) {
			$data = $this->get_user();
		}
		
		$query      = $db->simple_select("userfields", "ufid", "ufid = {$user['uid']}");
		$check = $db->fetch_field($query, "ufid");
		$db->free_result($query);
		
		if (!$check) {
			$userfield['ufid'] = $user['uid'];
		}
		
		// No Facebook ID? Sync it too!
		if (!$user['myfb_uid'] and $data['id']) {
			$update['myfb_uid'] = $data['id'];
		}
		
		// Avatar
		if ($user['fbavatar'] and $data['id'] and $mybb->settings['myfbconnect_fbavatar']) {
			
			list($maxwidth, $maxheight) = explode('x', my_strtolower($mybb->settings['maxavatardims']));
			
			$update["avatar"]     = $db->escape_string("http://graph.facebook.com/{$data['id']}/picture?width={$maxwidth}&height={$maxheight}");
			$update["avatartype"] = "remote";
			
			// Copy the avatar to the local server (work around remote URL access disabled for getimagesize)
			$file     = fetch_remote_file($update["avatar"]);
			$tmp_name = $mybb->settings['avataruploadpath'] . "/remote_" . md5(random_str());
			$fp       = @fopen($tmp_name, "wb");
			
			if ($fp) {
				
				fwrite($fp, $file);
				fclose($fp);
				list($width, $height, $type) = @getimagesize($tmp_name);
				@unlink($tmp_name);
				
				if (!$type) {
					$avatar_error = true;
				}
				
			}
			
			if (!$avatar_error) {
				
				if ($width and $height and $mybb->settings['maxavatardims'] != "") {
					
					if (($maxwidth and $width > $maxwidth) or ($maxheight and $height > $maxheight)) {
						$avatardims = $maxheight . "|" . $maxwidth;
					}
					
				}
				
				if ($width > 0 and $height > 0 and !$avatardims) {
					$avatardims = $width . "|" . $height;
				}
				
				$update["avatardimensions"] = $avatardims;
				
			}
			else {
				$update["avatardimensions"] = $maxheight . "|" . $maxwidth;
			}
		}
		
		// Birthday
		if ($user['fbbday'] and $data['birthday'] and $mybb->settings['myfbconnect_fbbday']) {
			
			$birthday           = explode("/", $data['birthday']);
			$birthday['0']      = ltrim($birthday['0'], '0');
			$update["birthday"] = $birthday['1'] . "-" . $birthday['0'] . "-" . $birthday['2'];
			
		}
		
		// Cover, if Profile Picture plugin is installed
		if ($user['fbavatar'] and $data['cover']['source'] and $mybb->settings['myfbconnect_fbavatar'] and $db->field_exists("profilepic", "users")) {
			
			$cover                    = $data['cover']['source'];
			$update["profilepic"]     = str_replace('/s720x720/', '/p851x315/', $cover);
			$update["profilepictype"] = "remote";
			
			if ($mybb->usergroup['profilepicmaxdimensions']) {
				
				list($maxwidth, $maxheight) = explode("x", my_strtolower($mybb->usergroup['profilepicmaxdimensions']));
				$update["profilepicdimensions"] = $maxwidth . "|" . $maxheight;
				
			}
			else {
				$update["profilepicdimensions"] = "851|315";
			}
			
		}
		
		// Sex
		if ($user['fbsex'] and $data['gender'] and $mybb->settings['myfbconnect_fbsex']) {
			
			if ($db->field_exists($sexid, "userfields")) {
				
				if ($data['gender'] == "male") {
					$userfield[$sexid] = $lang->myfbconnect_male;
				}
				else if ($data['gender'] == "female") {
					$userfield[$sexid] = $lang->myfbconnect_female;
				}
				
			}
		}
		
		// Name and last name
		if ($user['fbdetails'] and $data['name'] and $mybb->settings['myfbconnect_fbdetails']) {
			
			if ($db->field_exists($detailsid, "userfields")) {
				$userfield[$detailsid] = $db->escape_string($data['name']);
			}
			
		}
		
		// Bio
		if ($user['fbbio'] and $data['bio'] and $mybb->settings['myfbconnect_fbbio']) {
			
			if ($db->field_exists($bioid, "userfields")) {
				$userfield[$bioid] = $db->escape_string(htmlspecialchars_decode(my_substr($data['bio'], 0, 400, true)));
			}
			
		}
		
		// Location
		if ($user['fblocation'] and $data['location']['name'] and $mybb->settings['myfbconnect_fblocation']) {
			
			if ($db->field_exists($locationid, "userfields")) {
				$userfield[$locationid] = $db->escape_string($data['location']['name']);
			}
			
		}
		
		if ($update) {			
			$query = $db->update_query("users", $update, "uid = {$user['uid']}");
		}
		
		// Make sure we can do it
		if ($userfield) {
			
			if ($userfield['ufid']) {
				$query = $db->insert_query("userfields", $userfield);
			}
			else {
				$query = $db->update_query("userfields", $userfield, "ufid = {$user['uid']}");
			}
			
		}
		
		return true;
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
	 * Remembers the page where the plugin should redirect to once finishing authenticating
	 */
	public function remember_page()
	{
		if (!session_id()) {
			session_start();
		}
		
		$_SESSION['myfbconnect']['return_to_page'] = $_SERVER['HTTP_REFERER'];
		
		return true;
	}
	
	/**
	 * Redirects the user to the page he came from
	 */
	public function redirect($url = '', $title = '', $message = '')
	{
		if (!session_id()) {
			session_start();
		}
		
		if (!$url and $_SESSION['myfbconnect']['return_to_page']) {
			$url = $_SESSION['myfbconnect']['return_to_page'];
		}
		else {
			$url = "index.php";
		}
		
		if ($url and strpos($url, "myfbconnect.php") === false) {
			$url = htmlspecialchars_uni($url);
		}
		else {
			$url = "index.php";
		}
		
		redirect($url, $message, $title);
		
		return true;
	}
	
	/**
	 * Generates a bug report and inserts it into the database and shows an error to the user
	 */
	public function generate_report($e)
	{
		global $db, $lang;
		
		$report = array(
			'dateline' => TIME_NOW,
			'code' => (int) $e->getCode(),
			'file' => $db->escape_string($e->getFile()),
			'line' => (int) $e->getLine(),
			'message' => $db->escape_string($e->getMessage()),
			'trace' => $db->escape_string($e->getTraceAsString())
		);
		
		$db->insert_query('myfbconnect_reports', $report);
		
		error($lang->myfbconnect_error_report_generated);
		
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
