<?php

class Phorum_user extends DatabaseObject {
	var $m_keyColumnNames = array('user_id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array(
		'user_id',
		'fk_campsite_user_id',
		'username',
		'password',
		'cookie_sessid_lt',
		'sessid_st',
		'sessid_st_timeout',
		'password_temp',
		'email',
		'email_temp',
		'hide_email',
		'active',
		'user_data',
		'signature',
		'threaded_list',
		'posts',
		'admin',
		'threaded_read',
		'date_added',
		'date_last_active',
		'last_active_forum',
		'hide_activity',
		'show_signature',
		'email_notify',
		'pm_email_notify',
		'tz_offset',
		'is_dst',
		'user_language',
		'user_template',
		'moderator_data',
		'moderation_email');

	/**
	 * Constructor
	 *
	 * @param int $p_userId
	 */
	function Phorum_user($p_userId = null)
	{
		global $PHORUM;
		$this->m_dbTableName = $PHORUM['user_table'];
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data["user_id"] = $p_userId;
		if (!is_null($p_userId)) {
			$this->fetch();
		}
	} // constructor


  	/**
  	 * Create the user.  The user cannot be created if the user is
  	 * banned or the user name or email already exists.  The optional
  	 * $p_userId parameter is there for the cases where Phorum is run
  	 * inside of another master application and you want to use the
  	 * master application user ID for the Phorum ID.
  	 *
  	 * @param string $p_username
	 * @param string $p_password
  	 * @param string $p_email
  	 * @param int $p_userId
  	 * @param bool $p_encryptedPassword
  	 * @return boolean
  	 */
  	function create($p_username, $p_password, $p_email, $p_userId = null,
  	                $p_encryptedPassword = false)
  	{
		$userdata = array();

	   	if (Phorum_user::UserNameExists($p_username)) {
	   		return false;
	   	}

	   	if (Phorum_user::EmailExists($p_email)) {
	   		return false;
	   	}

		if (Phorum_user::IsBanned($p_username, $p_email)) {
			return false;
		}

		if (!is_null($p_userId) && is_numeric($p_userId)) {
			$tmpUser = new Phorum_user($p_userId);
			$userdata['user_id'] = $p_userId;
			$userdata['fk_campsite_user_id'] = $p_userId;
			if ($tmpUser->exists()) {
				unset($userdata['user_id']);
			}
		}

		$userdata['username'] = $p_username;
		$userdata['password'] = $p_encryptedPassword ? $p_password : sha1($p_password);
		$userdata['email'] = $p_email;
		$userdata['date_added'] = time();
		$userdata['date_last_active'] = time();
		$userdata['hide_email'] = true;
		$userdata['active'] = PHORUM_USER_ACTIVE;

		// Create the user
		$this->m_data['user_id'] = phorum_db_user_add( $userdata );

		// Refresh the object from the database.
		$this->fetch();

		return true;
	} // fn create


  	/**
  	 * Delete the user.
  	 * @return boolean
  	 */
  	function delete()
  	{
  		global $PHORUM;
  		$PHORUM['DATA']['LANG']['AnonymousUser'] = 'Anonymous User';
  		if (phorum_db_user_delete($this->getUserId())) {
  			$this->m_exists = false;
  			return true;
  		} else {
  			return false;
  		}
  	} // fn delete


  	/**
  	 * Get the user by their user name.
  	 *
  	 * @param string $p_username
  	 * @return Phorum_user
  	 */
  	function GetByUserName($p_username)
  	{
  		$user =& new Phorum_user();
  		$user->setKey('username');
  		$user->m_data['username'] = $p_username;
  		$user->fetch();
  		if ($user->exists()) {
  			return $user;
  		} else {
  			return null;
  		}
  	} // fn GetByUserName


	/**
	 * Return TRUE if the campsite user exists in the user table.
	 *
	 * @param int $p_userid
	 * @return boolean
	 */
	function CampUserExists($p_userid)
	{
		return (phorum_db_user_check_field( "fk_campsite_user_id", $p_userid ));
	} // fn CampUserExists


  	/**
  	 * Return TRUE if the user name exists in the user table.
  	 *
  	 * @param string $p_username
  	 * @return boolean
  	 */
  	function UserNameExists($p_username)
  	{
		// Check if the username and email address don't already exist.
	    return (phorum_db_user_check_field( "username", $p_username ));
  	} // fn UserNameExists


  	/**
  	 * Return TRUE if the email address already exists in the
  	 * user table.
  	 *
  	 * @param string $p_email
  	 * @return boolean
  	 */
  	function EmailExists($p_email)
  	{
	    return (phorum_db_user_check_field( "email", $p_email ));
  	} // fn EmailExists


  	/**
  	 * Return TRUE if any one of the given username, email, or IP address
  	 * is banned.
  	 *
  	 * @param string $p_username
  	 * @param string $p_email
  	 */
  	function IsBanned($p_username, $p_email)
  	{
  		global $PHORUM;

		$conn = phorum_db_mysql_connect();

	    // Check if username is banned.
	    $sql = "SELECT COUNT(*) as matches FROM ".$PHORUM['banlist_table']
	    		." WHERE type=".PHORUM_BAD_NAMES
	    		." AND string='".mysql_escape_string($p_username)."'";
	    $result = mysql_query($sql, $conn);
	    $row = mysql_fetch_assoc($result);
	    if ($row['matches'] > 0) {
	    	return true;
	    }

	    // Check if email is banned.
	    $sql = "SELECT COUNT(*) as matches FROM ".$PHORUM['banlist_table']
	    		." WHERE type=".PHORUM_BAD_EMAILS
	    		." AND string='".mysql_escape_string($p_email)."'";
	    $result = mysql_query($sql, $conn);
	    $row = mysql_fetch_assoc($result);
	    if ($row['matches'] > 0) {
	    	return true;
	    }

	    // Check if IP address is banned.
	    $ipaddr = $_SERVER['REMOTE_ADDR'];
		// Fetch the settings and pretend they were returned to
		// us instead of setting a global variable.
		phorum_db_load_settings();
		$settings = $PHORUM['SETTINGS'];
        if ($settings["dns_lookup"]) {
            $resolved = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
            if (!empty($resolved) && $resolved != $_SERVER["REMOTE_ADDR"]) {
                $ipaddr = $resolved;
            }
        }
	    $sql = "SELECT COUNT(*) as matches FROM ".$PHORUM['banlist_table']
	    		." WHERE type=".PHORUM_BAD_IPS
	    		." AND string='".mysql_escape_string($ipaddr)."'";
	    $result = mysql_query($sql, $conn);
	    $row = mysql_fetch_assoc($result);
	    if ($row['matches'] > 0) {
	    	return true;
	    }

	    return false;
  	} // fn IsBanned


  	/**
  	 * Return the unique user ID.
  	 *
  	 * @return int
  	 */
  	function getUserId()
  	{
  		return $this->m_data['user_id'];
  	} // fn getUserId


  	/**
  	 * Return the username.
  	 *
  	 * @return string
  	 */
  	function getUserName()
  	{
  		return $this->m_data['username'];
  	} // fn getUserName


  	/**
  	 * Return the password.  Can be encrypted or plain text
  	 * depending on how you have Phorum set up.
  	 *
  	 * @return string
  	 */
  	function getPassword()
  	{
  		return $this->m_data['password'];
  	} // fn getPassword


	/**
	 * Set the password for the phorum user.
	 *
	 * @param string $p_password
	 * @return boolean
	 */
	function setPassword($p_password)
	{
		return $this->setProperty('password', $p_password);
	}  // fn setPassword


  	/**
  	 * Return the user's email address.
  	 *
  	 * @return string
  	 */
  	function getEmail()
  	{
  		return $this->m_data['email'];
  	} // fn getEmail


	/**
	 * Set the email.
	 *
	 * @param string $p_email
	 * @return boolean
	 */
	function setEmail($p_email)
	{
		return $this->setProperty('email', $p_email);
	}


  	/**
  	 * Return whether the user has been approved or not.
  	 *
  	 * @return int
  	 */
  	function getActivationStatus()
  	{
  		return $this->m_data['active'];
  	} // fn getActivationStatus


  	/**
  	 * Return TRUE if the user is an administrator.
  	 *
  	 * @return boolean
  	 */
  	function isAdmin()
  	{
  		return $this->m_data['admin'];
  	} // fn isAdmin


  	/**
  	 * Get the number of messages this user has posted.
  	 *
  	 * @return int
  	 */
  	function getNumPosts()
  	{
  		return $this->m_data['posts'];
  	} // fn getNumPosts


} // class Phorum_user

?>
