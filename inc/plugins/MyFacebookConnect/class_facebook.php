<?php

/**
 * A bridge between MyBB with Facebook, featuring login, registration and more.
 *
 * @package Main API class
 * @version 3.1
 */

class MyFacebook
{
	// The fallback URL where Facebook redirects users
	private $fallback;
	
	// The main object populated upon initialization
	public $facebook;
	
	// The access token of the current user. Populated upon initialization
	public $access_token;
	
	/**
	 * Contructor
	 */
	public function __construct()
	{
		global $mybb, $lang;
		
		if (!$lang->myfbconnect) {
			$lang->load('myfbconnect');
		}
		
		if (!session_id()) {
			session_start();
		}
		
		$this->load_api();
		$this->set_fallback();
		
		if ($_SESSION['facebook_access_token']) {
			$this->facebook->setDefaultAccessToken($_SESSION['facebook_access_token']);
		}
		
		$this->access_token = $this->facebook->getDefaultAccessToken();
		
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
		
		if (!$mybb->settings['myfbconnect_appid'] or !$mybb->settings['myfbconnect_appsecret']) {
			error($lang->myfbconnect_error_no_config_found);
		}
		
		try {
			require_once MYBB_ROOT . "myfbconnect/src/Facebook/autoload.php";
		}
		catch (Exception $e) {
			$this->generate_report($e);
		}
		
		// Create our application instance
		$this->facebook = new Facebook\Facebook([
			'app_id' => $mybb->settings['myfbconnect_appid'],
			'app_secret' => $mybb->settings['myfbconnect_appsecret'],
			'default_graph_version' => 'v2.8',
			'persistent_data_handler' => 'session'
		]);
		
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
		
		$permissions = [
			'public_profile',
			'user_about_me',
			'user_birthday',
			'user_location',
			'email'
		];
		
		// Get the URL and redirect the user
		$redirect = $this->facebook->getRedirectLoginHelper()->getLoginUrl($this->fallback, $permissions);
		
		header("Location: " . $redirect);
		
		return true;
	}
	
	public function save_token()
	{
		global $lang;
		
		$helper = $this->facebook->getRedirectLoginHelper();
		
		try {
			$access_token = $helper->getAccessToken();
		}
		catch(Facebook\Exceptions\FacebookSDKException $e) {
			$this->generate_report($e);
		}

		if (isset($access_token)) {
			$_SESSION['facebook_access_token'] = (string) $access_token;
		}
		else {
			error($lang->myfbconnect_error_missing_access_token);
		}
		
		$oAuth2Client = $this->facebook->getOAuth2Client();

		// Exchanges a short-lived access token for a long-lived one
		$_SESSION['facebook_access_token'] = (string) $oAuth2Client->getLongLivedAccessToken($access_token);
		
		if ($_SESSION['facebook_access_token']) {
			return $this->facebook->setDefaultAccessToken($_SESSION['facebook_access_token']);
		}
		
		return false;	
	}
	
	/**
	 * Attempts to get the authenticated user's data
	 */
	public function get_user($fields = '')
	{
		global $lang;
		
		if (!$fields) {
			$fields = "id,name,email,cover,birthday,website,gender,about,location,verified";
		}
		
		try {
			$response = $this->facebook->get('/me?fields=' . $fields);
		}
		catch (Facebook\Exceptions\FacebookSDKException $e) {
			
			// If the access token is invalid, expired or non-matching, reauthenticate automatically
			if (method_exists($e, 'getResponseData') and in_array($e->getResponseData()['error']['error_subcode'], [458, 463, 464, 467])) {
				return $this->authenticate();
			};
			
			$this->generate_report($e);
		}
		
		$this->user = ($response) ? $response->getDecodedBody() : null;
		
		return $this->user;
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
		$db->update_query("sessions", [
			"uid" => $user['uid']
		], "sid='" . $session->sid . "'");
		
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
		
		global $mybb, $session, $lang;
		
		require_once MYBB_ROOT . "inc/datahandlers/user.php";
		$userhandler = new UserDataHandler("insert");
		
		$plength = 8;
		if ($mybb->settings['minpasswordlength'] and $mybb->settings['minpasswordlength'] > $plength) {
			$plength = (int) $mybb->settings['minpasswordlength'];
		}
		
		$password = random_str($plength, true); // 2nd argument fixes complex password issue
		
		$new_user = [
			"username" => $user['name'],
			"password" => $password,
			"password2" => $password,
			"email" => $user['email'],
			"email2" => $user['email'],
			"usergroup" => (int) $mybb->settings['myfbconnect_usergroup'],
			"regip" => $session->ipaddress,
			"longregip" => my_ip2long($session->ipaddress),
			"options" => [
				"hideemail" => 1
			]
		];
		
		$userhandler->set_data($new_user);
		if ($userhandler->validate_user()) {
			
			$user_info = $userhandler->insert_user();
			
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
				
				$thingsToReplace = [
					"{user}" => $user_info['username'],
					"{password}" => $password
				];
				
				// Replace what needs to be replaced
				foreach ($thingsToReplace as $find => $replace) {
					$message = str_replace($find, $replace, $message);
				}
				
				$pm = [
					"subject" => $subject,
					"message" => $message,
					"fromid" => $fromid,
					"toid" => [
						$user_info['uid']
					]
				];
				
				// Some defaults :)
				$pm['options'] = [
					"signature" => 1
				];
				
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
			return $user_info;
			
		}
		else {
			return [
				'error' => $userhandler->get_friendly_errors()
			];
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
		
		$update = [
			"myfb_uid" => md5($id)
		];
		
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
		
		$update = [
			"myfb_uid" => 0
		];
		
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
	public function process()
	{
		global $mybb, $db, $session, $lang;
		
		// Just verified allowed?
		if ($mybb->settings['myfbconnect_verifiedonly'] and $this->user['verified'] !== 1) {
			error($lang->myfbconnect_error_verified_only);
		}
		
		if (!$this->user['id']) {
			error($lang->myfbconnect_error_no_id_provided);
		}
		
		$extra_sql = '';
		if ($this->user['email']) {
			$extra_sql .= " OR email = '" . $db->escape_string($this->user['email']) . "'";
		}
		
		/*
		* Solves a confirmed security issue discussed in http://www.mybboost.com/thread-release-myfacebook-connect-3-0
		* The user identifier is casted as int but PHP automatically scales it down to a 2 GB value on 32-bit systems
		* The already-with-us check fails and treats the authenticated user as another account, making it vulnerable.
		* DO NOT CAST AS (int) THIS USER IDENTIFIER!
		*/
		$hashed_id = md5($this->user['id']);
		
		// Let's see if you are already with us
		$query   = $db->simple_select(
			"users",
			"*",
			"myfb_uid = '{$hashed_id}' OR myfb_uid = '{$this->user['id']}'{$extra_sql}",
			[
				"limit" => 1
			]
		);
		$account = $db->fetch_array($query);
		
		$message = $lang->myfbconnect_redirect_logged_in;
		
		// Link
		if ($this->user['email'] and $account['email'] == $this->user['email'] and !$account['myfb_uid']) {
			$this->link_user($account, $this->user['id']);
		}
		// Register
		else if (!$account) {
			
			if (!$mybb->settings['myfbconnect_fastregistration']) {
				return header("Location: myfbconnect.php?action=register");
			}
			
			$account = $this->register($this->user);
			
			if ($account['error']) {
				return $account;
			}
			else {
			
				// Set some defaults
				$toCheck = ['fbavatar', 'fbbday', 'fbsex', 'fbdetails', 'fbbio', 'fblocation'];
				foreach ($toCheck as $setting) {
				
					$tempKey = 'myfbconnect_' . $setting;
					$new_settings[$setting] = $mybb->settings[$tempKey];
					
				}
				
				$account = array_merge($account, $new_settings);
				
			}
			
			$message = $lang->myfbconnect_redirect_registered;
			
		}
		
		// Versions prior to 3.0 do not have hashed IDs. Unset myfb_uid so sync() can adjust it automatically
		if ($account['myfb_uid'] == $this->user['id']) {
			unset($account['myfb_uid']);
		}
		
		// Login
		$this->login($account);
		
		// Sync
		$this->sync($account);
		
		$title = $lang->sprintf($lang->myfbconnect_redirect_title, $account['username']);
		
		// Redirect
		return $this->redirect('', $title, $message);
	}
	
	/**
	 * Synchronizes Facebook's data with MyBB's data
	 */
	public function sync($user)
	{
		if (!$user['uid']) {
			return false;
		}
		
		global $mybb, $db, $session, $lang;
		
		$update     = [];
		$userfield  = [];
		
		$detailsid  = "fid" . (int) $mybb->settings['myfbconnect_fbdetailsfield'];
		$locationid = "fid" . (int) $mybb->settings['myfbconnect_fblocationfield'];
		$bioid      = "fid" . (int) $mybb->settings['myfbconnect_fbbiofield'];
		$sexid      = "fid" . (int) $mybb->settings['myfbconnect_fbsexfield'];
		
		if (!$this->user) {
			$this->get_user();
		}
		
		$query = $db->simple_select("userfields", "ufid", "ufid = {$user['uid']}");
		$check = $db->fetch_field($query, "ufid");
		
		if (!$check) {
			$userfield['ufid'] = $user['uid'];
		}
		
		// No Facebook ID? Sync it too!
		if (!$user['myfb_uid'] and $this->user['id']) {
			$update['myfb_uid'] = md5($this->user['id']);
		}
		
		// Avatar
		if ($user['fbavatar'] and $this->user['id'] and $mybb->settings['myfbconnect_fbavatar']) {
			
			list($maxwidth, $maxheight) = explode('x', my_strtolower($mybb->settings['maxavatardims']));
			
			$update["avatar"]     = $db->escape_string("https://graph.facebook.com/{$this->user['id']}/picture?width={$maxwidth}&height={$maxheight}");
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
		if ($user['fbbday'] and $this->user['birthday'] and $mybb->settings['myfbconnect_fbbday']) {
			
			$birthday           = explode("/", $this->user['birthday']);
			$birthday['0']      = ltrim($birthday['0'], '0');
			$update["birthday"] = $birthday['1'] . "-" . $birthday['0'] . "-" . $birthday['2'];
			
		}
		
		// Cover, if Profile Picture plugin is installed
		if ($user['fbavatar'] and $this->user['cover']['source'] and $mybb->settings['myfbconnect_fbavatar'] and $db->field_exists("profilepic", "users")) {
			
			$cover                    = $this->user['cover']['source'];
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
		if ($user['fbsex'] and $this->user['gender'] and $mybb->settings['myfbconnect_fbsex']) {
			
			if ($db->field_exists($sexid, "userfields")) {
				
				if ($this->user['gender'] == "male") {
					$userfield[$sexid] = $lang->myfbconnect_male;
				}
				else if ($this->user['gender'] == "female") {
					$userfield[$sexid] = $lang->myfbconnect_female;
				}
				
			}
		}
		
		// Name and last name
		if ($user['fbdetails'] and $this->user['name'] and $mybb->settings['myfbconnect_fbdetails']) {
			
			if ($db->field_exists($detailsid, "userfields")) {
				$userfield[$detailsid] = $db->escape_string($this->user['name']);
			}
			
		}
		
		// Bio
		if ($user['fbbio'] and $this->user['about'] and $mybb->settings['myfbconnect_fbbio']) {
			
			if ($db->field_exists($bioid, "userfields")) {
				$userfield[$bioid] = $db->escape_string(htmlspecialchars_decode(my_substr($this->user['about'], 0, 400, true)));
			}
			
		}
		
		// Location
		if ($user['fblocation'] and $this->user['location']['name'] and $mybb->settings['myfbconnect_fblocation']) {
			
			if ($db->field_exists($locationid, "userfields")) {
				$userfield[$locationid] = $db->escape_string($this->user['location']['name']);
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
			$update   = [
				"additionalgroups" => implode(",", array_filter($groups))
			];
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
			
			$update = [
				"additionalgroups" => implode(",", $groups)
			];
			$db->update_query("users", $update, "uid = {$user['uid']}");
			
		}
		
		return true;
	}
	
	/**
	 * Redirects the user to the page he came from
	 */
	public function redirect($url = '', $title = '', $message = '')
	{	
		if (!$url and $_SESSION['myfbconnect']['return_to_page']) {
			$url = $_SESSION['myfbconnect']['return_to_page'];
		}
		else if (!$url) {
			$url = "index.php";
		}
		
		if ($url and strpos($url, "myfbconnect.php") === false) {
			$url = htmlspecialchars_uni($url);
		}
		else {
			$url = "index.php";
		}
		
		return redirect($url, $message, $title);
	}
	
	/**
	 * Generates a bug report and inserts it into the database and shows an error to the user
	 */
	public function generate_report($e)
	{
		global $mybb, $db, $lang;
		
		$report = [
			'dateline' => TIME_NOW,
			'code' => (int) $e->getCode(),
			'file' => $db->escape_string($e->getFile()),
			'line' => (int) $e->getLine(),
			'message' => $db->escape_string($e->getMessage()),
			'trace' => $db->escape_string($e->getTraceAsString())
		];
		
		$db->insert_query('myfbconnect_reports', $report);
		
		$extra = ($mybb->usergroup['cancp']) ? $lang->myfbconnect_error_report_generated_admin : $lang->myfbconnect_error_report_generated_user;
		
		return error($lang->sprintf($lang->myfbconnect_error_report_generated, $e->getMessage(), $extra));
	}
	
}
