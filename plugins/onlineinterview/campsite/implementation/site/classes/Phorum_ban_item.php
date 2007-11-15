<?php
include_once("Phorum_user.php");
include_once("Phorum_message.php");

class Phorum_ban_item extends DatabaseObject {
	var $m_keyColumnNames = array('id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array(
		"id",
		"forum_id",
		"type",
		"pcre",
		"string");

	var $m_allowedTypes = array(PHORUM_BAD_IPS,
								PHORUM_BAD_NAMES,
								PHORUM_BAD_EMAILS,
								PHORUM_BAD_WORDS,
								PHORUM_BAD_USERID,
								PHORUM_BAD_SPAM_WORDS);

	/**
	 * A ban item is string that, if matched, will prevent a message from
	 * being posted.
	 *
	 * @param int $p_banId
	 *
	 * @return Phorum_ban
	 */
	function Phorum_ban_item($p_banId = null)
	{
		global $PHORUM;
		$this->m_dbTableName = $PHORUM['banlist_table'];
		$this->m_data['id'] = $p_banId;
		if (!is_null($p_banId)) {
			$this->fetch();
		}
	} // fn Phorum_ban_item


	/**
	 * Create a ban item.
	 *
	 * @param int $p_type
	 * 		Can be one of:
	 * 		PHORUM_BAD_IPS
	 * 		PHORUM_BAD_NAMES
	 * 		PHORUM_BAD_EMAILS
	 * 		PHORUM_BAD_WORDS
	 * 		PHORUM_BAD_USERID
	 * 		PHORUM_BAD_SPAM_WORDS
	 *
	 * @param boolean $p_isRegex
	 * 		Set to TRUE if $p_matchString is a regular expression,
	 * 		set to FALSE if	it isnt.
	 *
	 * @param string $p_matchString
	 * 		String to match to see if something is banned.
	 *
	 * @param int $p_forumId
	 * 		If the forum ID is set to zero, the ban will apply to all forums.
	 * 		If it is set to a number greater that zero (a forum ID),
	 * 		then the ban will only apply to that forum.
	 *
	 * @return boolean
	 */
	function create($p_type, $p_isRegex, $p_matchString, $p_forumId = 0) {
		global $PHORUM;
	    if (!is_numeric($p_type) || !is_numeric($p_forumId)
	    	|| !in_array($p_type, $this->m_allowedTypes)) {
	    	return false;
	    }
	    $p_isRegex = $p_isRegex ? "1": "0";

//	    if ($p_type == PHORUM_BAD_IPS) {
//	   		// Fetch the settings and pretend they were returned to
//			// us instead of setting a global variable.
//			phorum_db_load_settings();
//			$settings = $PHORUM['SETTINGS'];
//
//			// Lookup the IP address, convert to hostname
//	        if ($settings["dns_lookup"]) {
//	            $resolved = @gethostbyaddr($p_matchString);
//	            if (!empty($resolved) && ($resolved != $p_matchString) ) {
//	                $p_matchString = $resolved;
//	            }
//	        }
//	    }

	    // Check if this ban item already exists
	    $repeats = Phorum_ban_item::GetBanItems($p_type, $p_isRegex, $p_matchString, $p_forumId);

	    // Add it if it doesnt exist
	    if (count($repeats) == 0) {
		    $columns = array("type" => $p_type,
		    				 "forum_id" => $p_forumId,
		    				 "string" => $p_matchString,
		    				 "pcre" => $p_isRegex);
			$success = parent::create($columns);
			return $success;
	    }
	    return true;
	} // fn create


	/**
	 * For those who want to update the whole record at once.
	 *
	 * @param int $p_type
	 * @param boolean $p_isRegex
	 * @param string $p_matchString
	 * @param int $p_forumId
	 */
	function update($p_type = null, $p_isRegex = null, $p_matchString = null, $p_forumId = null)
	{
		if (!is_null($p_type)) {
			$this->setProperty('type', $p_type, false);
		}
		if (!is_null($p_isRegex)) {
			$this->setProperty('pcre', $p_isRegex, false);
		}
		if (!is_null($p_matchString)) {
			$this->setProperty('string', $p_matchString, false);
		}
		if (!is_null($p_forumId)) {
			$this->setProperty('forum_id', $p_forumId, false);
		}
		$this->commit();
	} // fn update


	/**
	 * Delete the ban items matching the parameters
	 *
	 * @param int $p_type
	 * @param boolean $p_isRegex
	 * @param string $p_matchString
	 * @param int $p_forumId
	 *
	 * @return boolean
	 */
	function DeleteMatching($p_type, $p_isRegex, $p_matchString, $p_forumId = null)
	{
		global $g_ado_db;
		global $PHORUM;
		$whereStr = "";
	    $constraints = array();
	    if (!is_numeric($p_type) || !is_bool($p_isRegex) || !is_string($p_matchString)) {
	    	return false;
	    }
    	$constraints[] = "type = $p_type";
    	$p_isRegex = $p_isRegex ? '1' : '0';
    	$constraints[] = "pcre = $p_isRegex";
    	$constraints[] = "string='".mysql_real_escape_string($p_matchString)."'";
	    if (!is_null($p_forumId) && is_numeric($p_forumId)) {
	        $constraints[] = "forum_id = $p_forumId";
	    }
    	$whereStr = " WHERE ".implode(" AND ", $constraints);

	    $sql = "DELETE FROM {$PHORUM['banlist_table']} $whereStr LIMIT 1";
	    return $g_ado_db->Execute($sql);
	} // fn DeleteMatching


	/**
	 * This will return one of these constants:
	 * 		PHORUM_BAD_IPS
	 * 		PHORUM_BAD_NAMES
	 * 		PHORUM_BAD_EMAILS
	 * 		PHORUM_BAD_WORDS
	 * 		PHORUM_BAD_USERID
	 * 		PHORUM_BAD_SPAM_WORDS
	 *
	 * @return int
	 */
	function getType()
	{
		return $this->m_data['type'];
	} // fn getType


	/**
	 * Set the type of the ban item.  Can be one of:
	 * 		PHORUM_BAD_IPS
	 * 		PHORUM_BAD_NAMES
	 * 		PHORUM_BAD_EMAILS
	 * 		PHORUM_BAD_WORDS
	 * 		PHORUM_BAD_USERID
	 * 		PHORUM_BAD_SPAM_WORDS
	 *
	 * @param int $p_value
	 *
	 * @return boolean
	 */
	function setType($p_value)
	{
		if (in_array($p_value, $this->m_allowedTypes)) {
			return $this->setProperty('type', $p_value);
		}
		return false;
	} // fn setType


	/**
	 * If the forum ID is set to zero, the ban will apply to all forums.
	 * If it is set to a number greater that zero (a forum ID),
	 * then the ban will only apply to that forum.
	 *
	 * @return int
	 */
	function getForumId()
	{
		return $this->m_data['forum_id'];
	} // fn getForumId


	/**
	 * If the forum ID is set to zero, the ban will apply to all forums.
	 * If it is set to a number greater that zero (a forum ID),
	 * then the ban will only apply to that forum.
	 *
	 * @param int $p_value
	 * @return boolean
	 */
	function setForumId($p_value)
	{
		if (is_numeric($p_value)) {
			return $this->setProperty('forum_id', $p_value);
		}
		return false;
	} // fn setForumId


	/**
	 * Get the string to match which will determine whether something
	 * is banned.
	 *
	 * @return string
	 */
	function getMatchString()
	{
		return $this->m_data['string'];
	} // fn getMatchString


	/**
	 * Set the string to match in order for something to be banned.
	 *
	 * @param string $p_value
	 * @return boolean
	 */
	function setMatchString($p_value)
	{
		return $this->setProperty('string', $p_value);
	} // fn setMatchString


	/**
	 * Return TRUE if the match string is a regular expression.
	 *
	 * @return boolean
	 */
	function isRegex()
	{
		return $this->m_data['pcre'];
	} // fn isRegex


	/**
	 * Set whether the match string is a regular expression or not.
	 *
	 * @param boolean $p_value
	 * @return boolean
	 */
	function setIsRegex($p_value)
	{
		$p_value = $p_value ? '1' : '0';
		return $this->setProperty('pcre', $p_value);
	} // fn setIsRegex


	/**
	 * Return TRUE if the given string is banned according to this ban item.
	 *
	 * @param string $p_matchString
	 * 		The value to check.
	 *
	 * @param int $p_type
	 * 		Optional. If this item is not of this type, return value will
	 * 		be FALSE (i.e. not banned).
	 *
	 * @return boolean
	 * 		TRUE if given string matches the ban, FALSE if all is okay.
	 */
	function isBanned($p_matchString, $p_type = null)
	{
	    $type = $this->m_data['type'];
		if (!is_null($p_type) && ($type != $p_type)) {
			return false;
		}
	    $p_matchString = trim($p_matchString);

	    $string = $this->m_data['string'];
	    $isRegex = $this->m_data['pcre'];
	    if (!empty($p_matchString)) {
            if (!empty($string) && (
                 ($isRegex && @preg_match("/\b".$string."\b/i", $p_matchString)) ||
                 (!$isRegex && stristr($p_matchString , $string) && ($type != PHORUM_BAD_USERID) ) ||
                 ( ($type == PHORUM_BAD_USERID) && ($p_matchString == $string) ) ) ) {
                return true;
            }
	    }
	    return false;
	} // fn isBanned


	/**
	 * Retrieve the banlists for the current forum.
	 *
	 * @param int $p_type
	 * @param boolean $p_isRegex
	 * @param string $p_matchString
	 * @param int $p_forumId
	 * @return array
	 */
	function GetBanItems($p_type = null, $p_isRegex = null, $p_matchString = null, $p_forumId = null) {
		global $g_ado_db;
		global $PHORUM;

		$whereStr = "";
	    $constraints = array();
	    if (!is_null($p_type) && is_numeric($p_type)) {
	    	$constraints[] = "type = $p_type";
	    }
	    if (!is_null($p_isRegex) && is_bool($p_isRegex)) {
	    	$p_isRegex = $p_isRegex ? '1' : '0';
	    	$constraints[] = "pcre = $p_isRegex";
	    }
	    if (!is_null($p_matchString)) {
	    	$constraints[] = "string='".mysql_real_escape_string($p_matchString)."'";
	    }
	    if (!is_null($p_forumId) && is_numeric($p_forumId)) {
	    	if ($p_forumId > 0) {
	        	$constraints[] = "(forum_id = $p_forumId OR forum_id = 0)";
	    	}
	    }
	    if (count($constraints) > 0) {
	    	$whereStr = " WHERE ".implode(" AND ", $constraints);
	    }

	    $sql = "SELECT * FROM {$PHORUM['banlist_table']} $whereStr"
	    	  ." ORDER BY type, string";
	    $rows = $g_ado_db->GetAll($sql);
	    $retval = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$tmpObj =& new Phorum_ban_item();
				$tmpObj->fetch($row);
				$retval[] = $tmpObj;
			}
		}
		return $retval;
	} // fn GetBanItems


	/**
	 * Check if the given message and/or user is banned from posting.
	 *
	 * NOTE: This function could probably be optimized by doing most of the
	 * work in the MySQL database instead of in PHP.  In other words,
	 * do the work that isBanned() is doing in a database query, something
	 * like:
	 *
	 * $sql = "SELECT type FROM {$PHORUM['banlist_table']} "
	 *		   ." WHERE pcre=0 "
	 *		   ." AND (type=".PHORUM_BAD_IPS." AND string='$p_ip')"
	 *		   ." OR (type=".PHORUM_BAD_EMAILS." AND string='".$p_email"')"
	 *		   ." OR (type=".PHORUM_BAD_NAMES." AND string='$p_name')";
	 *
	 * @param Phorum_message $p_phorumMessage
	 * @param Phorum_user $p_phorumUser
	 * @param int $p_forumId
	 * @return boolean
	 */
	function IsPostBanned($p_phorumMessage, $p_phorumUser = null, $p_forumId = null)
	{
		global $PHORUM;
		static $bans;
		// Fetch the settings and pretend they were returned to
		// us instead of setting a global variable.
		phorum_db_load_settings();
		$settings = $PHORUM['SETTINGS'];

		// Cache the ban list.
		if (!isset($bans)) {
			// get the bans
			$bans = Phorum_ban_item::GetBanItems($p_forumId);
		}

		// Check if any of them match
		$banned = array();
		foreach ($bans as $ban) {
			switch ($ban->getType()) {
			case PHORUM_BAD_NAMES:
				if ($ban->isBanned($p_phorumMessage->getAuthor())) {
					$banned[PHORUM_BAD_NAMES] = PHORUM_BAD_NAMES;
				}
				if (!is_null($p_phorumUser) && $ban->isBanned($p_phorumUser->getUserName())) {
					$banned[PHORUM_BAD_NAMES] = PHORUM_BAD_NAMES;
				}
				break;
			case PHORUM_BAD_EMAILS:
				if ($ban->isBanned($p_phorumMessage->getEmail())) {
					$banned[PHORUM_BAD_EMAILS] = PHORUM_BAD_EMAILS;
				}
				if (!is_null($p_phorumUser) && $ban->isBanned($p_phorumUser->getEmail())) {
					$banned[PHORUM_BAD_EMAILS] = PHORUM_BAD_EMAILS;
				}
				break;
			case PHORUM_BAD_USERID:
				if (!is_null($p_phorumUser) && $ban->isBanned($p_phorumUser->getUserId())) {
					$banned[PHORUM_BAD_USERID] = PHORUM_BAD_USERID;
				}
				break;
			case PHORUM_BAD_IPS:
				if ($ban->isBanned($p_phorumMessage->getIpAddress())) {
					$banned[PHORUM_BAD_IPS] = PHORUM_BAD_IPS;
				}
				break;
			case PHORUM_BAD_SPAM_WORDS:
				if ($ban->isBanned($p_phorumMessage->getSubject())
					|| $ban->isBanned($p_phorumMessage->getBody())){
					$banned[PHORUM_BAD_SPAM_WORDS] = PHORUM_BAD_SPAM_WORDS;
				}
				break;
			}
		}
		if (count($banned) > 0) {
			return $banned;
		} else {
			return false;
		}
	} // fn IsPostBanned


} // class Phorum_ban_item

?>