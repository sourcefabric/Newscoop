<?php
// For phorum_email_moderators() and phorum_email_notice().
//include_once("./include/email_functions.php");

class Phorum_message extends DatabaseObject {
	var $m_keyColumnNames = array('message_id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array(
		"message_id",
		"forum_id",
		"thread",
		"parent_id",
		"author",
		"subject",
		"body",
		"email",
		"ip",
		"status",
		"msgid",
		"modifystamp",
		"user_id",
		"thread_count",
		"moderator_post",
		"sort",
		"datestamp",
		"meta",
		"viewcount",
		"closed");

	/**
	 * A Phorum_message is a message posted to a forum.
	 *
	 * @param int $p_messageId
	 * 		If specified, the message will be fetched from the database.
	 * @return Phorum_message
	 */
	function Phorum_message($p_messageId = null)
	{
		global $PHORUM;
		$this->m_dbTableName = $PHORUM['message_table'];
		$this->m_data["message_id"] = $p_messageId;
		if (!is_null($p_messageId)) {
			$this->fetch();
		}
	} // constructor


	/**
	 * Create a message.
	 *
	 * @param int $p_forumId
	 * @param string $p_subject
	 * @param string $p_body
	 * @param int $p_threadId
	 * @param int $p_parentId
	 * @param string $p_author
	 * @param string $p_email
	 * @param int $p_userId
	 * @return boolean
	 */
	function create($p_forumId, $p_subject ='', $p_body = '',
					$p_threadId = 0, $p_parentId = 0,
				    $p_author = '', $p_email = '', $p_userId = 0)
	{
		global $PHORUM;
		global $g_ado_db;

		if (!is_numeric($p_forumId)) {
			return null;
		}

		// Fetch the settings and pretend they were returned to
		// us instead of setting a global variable.
		phorum_db_load_settings();
		$settings = $PHORUM['SETTINGS'];

		// Required Input
		$message['forum_id'] = $p_forumId;

		// Optional input
		$message['body'] = $p_body;
		$message['subject'] = $p_subject;
		$message['thread'] = $p_threadId;
		$message['parent_id'] = $p_parentId;
		$message['author'] = $p_author;
		$message['email'] = $p_email;
		$message['user_id'] = $p_userId;

		// Defaults
		$message['sort'] = PHORUM_SORT_DEFAULT;
		$message['closed'] = 0;

		// ??? Whats that suffix for?
//		$suffix = preg_replace("/[^a-z0-9]/i", "", $PHORUM["name"]);
//		$message['msgid'] = md5(uniqid(rand())) . ".$suffix";
		$message['msgid'] = md5(uniqid(rand()));
		$message['moderator_post'] = '0';
		$message['datestamp'] = time();

		// Fetch the forum object -
		// we need it for the config values.
		$forumObj =& new Phorum_forum($p_forumId);
		if (!$forumObj->exists()) {
			return false;
		}

		// Set message workflow based on forum config.
		if ($forumObj->isModerated()) {
		    $message['status'] = PHORUM_STATUS_HOLD;
		} else {
		    $message['status'] = PHORUM_STATUS_APPROVED;
		}

		// Set user IP.
		$user_ip = $_SERVER["REMOTE_ADDR"];
		if ($settings["dns_lookup"]) {
		    $resolved = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
		    if (!empty($resolved)) {
		        $user_ip = $resolved;
		    }
		}
		$message["ip"] = $user_ip;

		phorum_db_post_message($message);

		// Update the thread count.
		$sql = "SELECT COUNT(*) as thread_count FROM ".$PHORUM['message_table']
			   ." WHERE forum_id=".$p_forumId
			   ." AND thread=".$message['thread']
			   ." AND status > 0";
		$threadCount = $g_ado_db->GetOne($sql);

		$sql = "UPDATE ".$PHORUM['message_table']
				." SET thread_count=".$threadCount;
		$g_ado_db->Execute($sql);

	    // Retrieve the message again because the database sets
	    // some values.
	    $message = phorum_db_get_message($message["message_id"], "message_id", true);
		$this->m_data = $message;

		$this->__updateThreadInfo();

        if (isset($PHORUM['user']['user_id'])) {
		    // Mark own message read.
	        phorum_db_newflag_add_read(array(0=>array(
	            "id"    => $message["message_id"],
	            "forum" => $message["forum_id"],
	        )));

	        // Update the number of messages the user has posted.
        	phorum_db_user_addpost();
        }

        // Actions for messages which are approved.
	    if ($message["status"] > 0)
	    {
	        // Update forum statistics,
	        // ??? Note: phorum_db_update_forum_stats requires global parameter-passing.
	        $PHORUM['forum_id'] = $p_forumId;
	        phorum_db_update_forum_stats(false, 1, $message["datestamp"]);

	        // Mail subscribed users.
	        //phorum_email_notice($message);
	    }

	    // Mail moderators.
	    if ($forumObj->emailModeratorsEnabled()) {
	        //phorum_email_moderators($message);
	    }

	    return true;
	} // fn create


	/**
	 * Delete a message.
	 *
	 * This code is a modified from phorum_db_delete_message().
	 * There was no way around it except changing the mysql.php file,
	 * and I'm not sure the change would be accepted, so it must be
	 * re-implemented here.
	 *
	 * @param int $p_mode The mode of deletion,
	 * 		PHORUM_DELETE_MESSAGE for reconnecting the children,
	 * 		PHORUM_DELETE_TREE for deleting the children
	 * @return void
	 */
	function delete($p_mode = PHORUM_DELETE_MESSAGE)
	{
		global $PHORUM;
		global $g_ado_db;
		unset($PHORUM['forum_id']);

	    $threadset = 0;
	    // get the parents of the message to delete.
	    $sql = "SELECT forum_id, message_id, thread, parent_id "
	    		." FROM {$PHORUM['message_table']} "
	    		." WHERE message_id = ".$this->m_data["message_id"];
	    $rec = $g_ado_db->GetRow($sql);

	    if ($p_mode == PHORUM_DELETE_TREE){
	        $mids = phorum_db_get_messagetree($this->m_data['message_id'], $rec['forum_id']);
	    } else {
	        $mids = $this->m_data['message_id'];
	    }

	    // unapprove the messages first so replies will not get posted
	    $sql = "UPDATE {$PHORUM['message_table']} "
	    		." SET status=".PHORUM_STATUS_HOLD
	    		." WHERE message_id IN ($mids)";
	    $g_ado_db->Execute($sql);

	    $thread = $rec['thread'];
	    if($thread == $this->m_data['message_id'] && $p_mode == PHORUM_DELETE_TREE){
	        $threadset = 1;
	    }else{
	        $threadset = 0;
	    }

	    if ($p_mode == PHORUM_DELETE_MESSAGE){
	        $count = 1;
	        // Change the children to point to their parent's parent.
	        // forum_id is in here for speed by using a key only
	        $sql = "UPDATE {$PHORUM['message_table']} "
	        		." SET parent_id=$rec[parent_id] "
	        		." WHERE forum_id=$rec[forum_id] "
	        		." AND parent_id=$rec[message_id]";
	        $g_ado_db->Execute($sql);
	    }else{
	        $count = count(explode(",", $mids));
	    }

	    // Delete the messages
	    $sql = "DELETE FROM {$PHORUM['message_table']} "
	    		." WHERE message_id IN ($mids)";
	    $g_ado_db->Execute($sql);

	    // start ft-search stuff
	    $sql = "DELETE FROM {$PHORUM['search_table']} "
	    	  ." WHERE message_id IN ($mids)";
	    $g_ado_db->Execute($sql);
	    // end ft-search stuff

	    $this->__updateThreadInfo();

	    // we need to delete the subscriptions for that thread too
	    $sql = "DELETE FROM {$PHORUM['subscribers_table']} "
	           ." WHERE forum_id > 0 AND thread=$thread";
	    $g_ado_db->Execute($sql);

	    // this function will be slow with a lot of messages.
	    // ??? Note: phorum_db_update_forum_stats() requires global parameter passing.
	    $PHORUM['forum_id'] = $this->m_data['forum_id'];
	    phorum_db_update_forum_stats(true);

		$this->m_exists = false;

		return explode(",", $mids);
	} // fn delete


    /**
	 * Update the thread info.
	 * Thread info is stored in the first message in the thread.
	 * The first message in the thread is the one where
	 * the message_id is equal to the thread_id.
	 *
	 * This code is mainly copied from phorum_update_thread_info(),
	 * but that function uses phorum_db_get_messages(),
	 * which uses external function calls to APIs outside of
	 * mysql.php, which we want to avoid.
     *
     * @return void
     */
	function __updateThreadInfo()
	{
	    $thread_id = $this->m_data['thread'];
		$searchConditions = array("thread" => $thread_id,
								  "forum_id" => $this->m_data['forum_id'],
								  "status" => PHORUM_STATUS_APPROVED);
		$messages = Phorum_message::GetMessages($searchConditions);
	    $thread_count = count($messages);

	    // If there are any messages left in the thread,
	    // then update the thread data.
	    if ($thread_count > 0) {
	        $threadBeginMessage = array();
	        $message_ids = array_keys($messages);
	        $parent_message = $messages[$thread_id];

	        // The most recent message in the thread.
            $recent_message = end($messages);

            // Updates to the first thread message.
	        $threadBeginMessage["thread_count"] = $thread_count;
	        $threadBeginMessage["modifystamp"] = $recent_message->m_data["datestamp"];
	        $threadBeginMessage["meta"] = $parent_message->m_data["meta"];
	        $threadBeginMessage["meta"]["recent_post"]["user_id"] = $recent_message->m_data["user_id"];
	        $threadBeginMessage["meta"]["recent_post"]["author"] = $recent_message->m_data["author"];
	        $threadBeginMessage["meta"]["recent_post"]["message_id"] = $recent_message->m_data["message_id"];
	        $threadBeginMessage["meta"]["message_ids"] = $message_ids;

	        // Used only for mods
	        // Get the message IDs of all messages in the thread,
	        // regardless of status.
	        //
	        // ??? Note: why are these stored in this way?
	        // Why not just grab these from the database when you
	        // want them - it would probably be just as fast.
    		$searchConditions = array("thread" => $thread_id,
    								  "forum_id" => $this->m_data['forum_id']);
    		$allMessages = Phorum_message::GetMessages($searchConditions);
	        $threadBeginMessage["meta"]["message_ids_moderator"] = array_keys($allMessages);

	        phorum_db_update_message($thread_id, $threadBeginMessage);
	    }
	} // fn __updateThreadInfo


	/**
	 * Get the unique message ID.
	 *
	 * The message ID is equal to the thread ID when the message
	 * is the beginning of the thread.
	 *
	 * @return unknown
	 */
	function getMessageId()
	{
		return $this->m_data['message_id'];
	} // fn getMessageId


	/**
	 * Get the forum that this message belongs to.
	 *
	 * @return int
	 */
	function getForumId()
	{
		return $this->m_data['forum_id'];
	} // fn getForumId


	/**
	 * Get the thread ID of the message.
	 *
	 * This is equal to the message ID when it is the first
	 * message in a thread.
	 *
	 * @return int
	 */
	function getThreadId()
	{
		return $this->m_data['thread'];
	} // fn getThreadId


	/**
	 * Get the ID of the message that this message is in response to
	 * (for when you are threading messages).
	 *
	 * @return int
	 */
	function getParentId()
	{
	    return $this->m_data['parent_id'];
	} // fn getParentId


	/**
	 * Get the subject of the message.
	 *
	 * @return string
	 */
	function getSubject()
	{
		return $this->m_data['subject'];
	} // fn getSubject


	/**
	 * Set the subject of the message.
	 *
	 * @param string $p_value
	 * @return boolean
	 */
	function setSubject($p_value)
	{
		return $this->setProperty('subject', $p_value);
	} // fn setSubject


	/**
	 * Get the body of the message.
	 *
	 * @return string
	 */
	function getBody()
	{
		return $this->m_data['body'];
	} // fn getBody


	/**
	 * Set the message text.
	 *
	 * @param string $p_value
	 * @return boolean
	 */
	function setBody($p_value)
	{
		return $this->setProperty('body', $p_value);
	} // fn setBody


	/**
	 * Get the IP address of the user who wrote the message.
	 *
	 * @return string
	 */
	function getIpAddress()
	{
		return $this->m_data['ip'];
	} // fn getIpAddress


	/**
	 * Get the author's name.
	 *
	 * @return string
	 */
	function getAuthor()
	{
	    return $this->m_data['author'];
	} // fn getAuthor


	/**
	 * Get the email address of the user who wrote the message.
	 *
	 * @return string
	 */
	function getEmail()
	{
	    return $this->m_data['email'];
	} // fn getEmail


	/**
	 * Get the workflow status of the message.
	 *
	 * Can be one of :
	 * 	PHORUM_STATUS_APPROVED
	 *  PHORUM_STATUS_HOLD
	 *  PHORUM_STATUS_HIDDEN
	 *
	 * @return int
	 */
	function getStatus()
	{
		return $this->m_data['status'];
	} // fn getStatus


	/**
	 * Set the workflow status of the message.
	 *
	 * @param int $p_value
	 *   Can be one of :
	 * 	   PHORUM_STATUS_APPROVED
	 *     PHORUM_STATUS_HOLD
	 *     PHORUM_STATUS_HIDDEN
	 *
	 * @return boolean
	 */
	function setStatus($p_value)
	{
		if ($p_value == PHORUM_STATUS_APPROVED
			|| $p_value == PHORUM_STATUS_HIDDEN
			|| $p_value == PHORUM_STATUS_HOLD)
		{
			return $this->setProperty('status', $p_value);
		} else {
			return false;
		}
	} // fn setStatus


	/**
	 * Get UNIX timestamp when the thread was last modified.
	 * Only useful for the first message in the thread.
	 *
	 * @return int
	 */
	function getThreadLastModified()
	{
		return $this->m_data['modifystamp'];
	} // fn getThreadLastModified


	/**
	 * Get the date that the message was created as a
	 * UNIX timestamp.
	 *
	 * @return int
	 */
	function getCreationDate()
	{
	    return $this->m_data['datestamp'];
	} // fn getCreationDate


	/**
	 * Get the number of times the message has been viewed.
	 *
	 * @return int
	 */
	function getNumViews()
	{
		return $this->m_data['viewcount'];
	} // fn getNumViews


	/**
	 * Return the number of messages in this thread.
	 * Only works for the root message in the thread.
	 *
	 * @return int
	 */
	function getNumMessagesInThread()
	{
		return $this->m_data['thread_count'];
	} // fn getNumMessagesInThread


	/**
	 * Get the user ID of the user who wrote the message.
	 *
	 * @return int
	 */
	function getUserId()
	{
		return $this->m_data['userid'];
	} // fn getUserId


	/**
	 * Return TRUE if the message was written by a moderator.
	 *
	 * @return boolean
	 */
	function isModeratorPost()
	{
		return $this->m_data['moderator_post'];
	} // fn isModeratorPost


	/**
	 * Set whether this message was written by a moderator.
	 *
	 * @param boolean $p_value
	 * @return boolean
	 */
	function setIsModeratorPost($p_value)
	{
		$p_value = $p_value ? "1" : "0";
		$this->setProperty("moderator_post", $p_value);
	} // fn setIsModeratorPost


	/**
	 * Enter description here...
	 *
	 * @return boolean
	 */
	function isClosed()
	{
		return $this->m_data['closed'];
	} // fn isClosed


	/**
	 * Subscribe the author of the message to the thread.
	 * @return void
	 */
	function subscribeToThread()
	{
	    // Subscribe user to the thread if requested.
        phorum_user_subscribe($this->m_data['user_id'],
        					  $this->m_data['forum_id'],
	       					  $this->m_data["thread"],
	       					  PHORUM_SUBSCRIPTION_MESSAGE);
	} // fn subscribeToThread


	/**
	 * Get the messages that match the given conditions.
	 * The conditions are AND'ed together.
	 *
	 * @param array $p_match
	 * 		An array of (column name => value to match)
	 * @param string $p_method
	 *     The way to combine the statements: can be
	 *     "AND", "OR", or "RAW".  RAW is for cases when
	 *     you want to type an SQL condition directly in
	 *     $p_match, for example:
	 *     Phorum_message::GetMessages("status > 0 AND author LIKE %foo%", "RAW");
	 *
	 * @return array
	 */
	function GetMessages($p_match, $p_method = "AND")
	{
		global $PHORUM;
		global $g_ado_db;
		if (!is_array($p_match)) {
			return null;
		}

		$p_method = strtoupper(trim($p_method));
		if (!in_array($p_method, array("AND", "OR", "RAW"))) {
		    return null;
		}

		if ($p_method != "RAW") {
    		foreach ($p_match as $columnName => $value) {
    			$parts[] = '`'.$columnName."`='".mysql_real_escape_string($value)."'";
    		}
    		$whereClause = implode(" $p_method ", $parts);
		} else {
		    $whereClause = $p_match;
		}
		$sql = "SELECT * FROM ".$PHORUM['message_table']
				." WHERE $whereClause"
				." ORDER BY message_id";
        $result = $g_ado_db->GetAll($sql);

	    $returnArray = array();
	    if (count($result) > 0){
            foreach ($result as $row) {
                // convert meta field
                if (empty($row["meta"])){
                    $row["meta"] = array();
                } else {
                    $row["meta"] = unserialize($row["meta"]);
                }
                $tmpMessage =& new Phorum_message();
                $tmpMessage->fetch($row);
                $returnArray[$row['message_id']] = $tmpMessage;
            }
	    }

	    return $returnArray;
	} // fn GetMessages


} // class Phorum_message
?>