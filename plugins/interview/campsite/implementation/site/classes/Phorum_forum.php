<?php

class Phorum_forum extends DatabaseObject {
	var $m_keyColumnNames = array('forum_id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array(
		"forum_id",

		// see setName()
		"name",

		// see setIsVisible()
		"active",

		"description",
		"template",
		"folder_flag",
		"parent_id",
		"list_length_flat",
		"list_length_threaded",

		// see setIsModerated()
		"moderation",

		"threaded_list",
		"threaded_read",
		"float_to_top",

		// see checkDuplicate()
		"check_duplicate",

		"allow_attachment_types",
		"max_attachment_size",
		"max_totalattachment_size",
		"max_attachments",

		// see setPublicPermissions()
		"pub_perms",

		// see setRegisteredUserPermissions()
		"reg_perms",

		"display_ip_address",
		"allow_email_notify",
		"language",
		"email_moderators",

		// see getNumMessages()
		"message_count",

		// see getNumSticky()
		"sticky_count",

		// see getNumThreads()
		"thread_count",

		// see getLastPostTime()
		"last_post_time",

        // The default value for display order is 0 for all forums.
        // So, in an unsorted forlder all the values are set to 0 until you move one.
		"display_order",

		"read_length",
		"vroot",

		// not used
		"edit_post",

		"template_settings",

		// see setCountViews()
		"count_views",
		"display_fixed",
		"reverse_threading",
		"inherit_id");

	function Phorum_forum($p_forumId = null)
	{
		global $PHORUM;
		$this->m_dbTableName = $PHORUM['forums_table'];
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['forum_id'] = $p_forumId;
		if (!is_null($p_forumId)) {
			$this->fetch();
		}
	} // constructor


	/**
	 * Create a forum.
	 *
	 * @return boolean
	 */
	function create()
	{
		$columns = array(
			'moderation'=>0,
			'email_moderators'=>0,
			'pub_perms'=>1,
			'reg_perms'=>15,

			// display
			'display_fixed'=>0,

			// display
			'template'=>'default',

			// ???
			'language'=>'english',

			// display
			'threaded_list'=>0,

			// display
			'threaded_read'=>0,

			// display
			'reverse_threading'=>0,

			// retreive
			'float_to_top'=>1,

			// Display
			// Used in phorum_db_get_thread_list() to determine how
			// many threads to show on a page.
			'list_length_flat'=>30,
			'list_length_threaded'=>15,

			// Used in phorum_db_get_messages() -
			// max number of messages to get.
			'read_length'=>30,

			'display_ip_address'=>0,
			'allow_email_notify'=>0,

			// Whether to check for duplicates when posting a message.
			// 0 => false
			// 1 => true
			'check_duplicate'=>1,

			// Count the number of times each message has been viewed
			// 0 => "No"
			// 1 => "Yes, show views added to subject"
			// 2 => "Yes, show views as extra column"
			'count_views'=>2,

			'max_attachments'=>0,
			'allow_attachment_types'=>'',
			'max_attachment_size'=>0,
			'max_totalattachment_size'=>0,

			// Virtual root folder
			'vroot'=>0);
		parent::create($columns);
	} // fn create


	/**
	 * Delete the forum and all of its messages and other data.
	 */
	function delete()
	{
		phorum_db_drop_forum($this->m_data['forum_id']);
		return true;
	} // fn delete


	/**
	 * Return the unique ID for this forum.
	 *
	 * @return int
	 */
	function getForumId()
	{
		return $this->m_data["forum_id"];
	} // fn getForumId


	/**
	 * Get the name of the forum.
	 *
	 * TODO: you should be able to translate this.
	 *
	 * @return string
	 */
	function getName()
	{
		return $this->m_data["name"];
	} // fn getName


	/**
	 * Set the name of the forum
	 *
	 * @param string $p_value
	 * @return boolean
	 */
	function setName($p_value)
	{
		return $this->setProperty("name", $p_value);
	} // fn setName


	/**
	 * Whether to check for duplicates when someone posts a message.
	 * If set to TRUE, check for duplicates.
	 * If set to FALSE, dont check for duplicates.
	 *
	 * @return boolean
	 */
	function checkDuplicates()
	{
		return $this->m_data["check_duplicate"];
	} // fn checkDuplicates


	/**
	 * Set whether to check for duplicates when someone posts a message.
	 * If set to TRUE, check for duplicates.
	 * If set to FALSE, dont check for duplicates.
	 *
	 * @param boolean $p_value
	 * @return boolean
	 */
	function setCheckDuplicates($p_value)
	{
		$p_value = $p_value ? "1" : "0";
		return $this->setProperty("check_duplicate", $p_value);
	} // fn setCheckDuplicates


	/**
	 * Get the permissions the general public has in this forum.
	 *
	 * @see setPublicPermissions()
	 * @return int
	 */
	function getPublicPermissions()
	{
		return $this->m_data['pub_perms'];
	} // fn getPublicPermissions


	/**
	 * Set the permissions the general public has in this forum.
	 *
	 * You can use zero or more of the following constants.  To use
	 * more than one, bitwise-OR them together:
	 * <pre>
	 *  - PHORUM_USER_ALLOW_READ
	 *  - PHORUM_USER_ALLOW_REPLY
	 *  - PHORUM_USER_ALLOW_EDIT
	 *  - PHORUM_USER_ALLOW_NEW_TOPIC
	 *  - PHORUM_USER_ALLOW_ATTACH
	 *  - PHORUM_USER_ALLOW_MODERATE_MESSAGES
	 *  - PHORUM_USER_ALLOW_MODERATE_USERS
	 *  - PHORUM_USER_ALLOW_FORUM_PROPERTIES
	 * </pre>
	 *
	 * @param int $p_permissions
	 * @return boolean
	 */
	function setPublicPermissions($p_permissions)
	{
		if (is_int($p_permissions)) {
			$this->setProperty('pub_perms', $p_permissions);
		} else {
			return false;
		}
	} // fn setPublicPermissions


	/**
	 * Get the permissions that registered users have in this forum.
	 *
	 * @see setRegisteredUserPermissions()
	 * @return int
	 */
	function getRegisteredUserPermissions()
	{
		return $this->m_data["reg_perms"];
	} // fn getRegisteredUserPermissions


	/**
	 * Set the permissions that registered users have in this forum.
	 *
	 * You can use zero or more of the following constants.  To use
	 * more than one, bitwise-OR them together:
	 * <pre>
	 *  - PHORUM_USER_ALLOW_READ
	 *  - PHORUM_USER_ALLOW_REPLY
	 *  - PHORUM_USER_ALLOW_EDIT
	 *  - PHORUM_USER_ALLOW_NEW_TOPIC
	 *  - PHORUM_USER_ALLOW_ATTACH
	 *  - PHORUM_USER_ALLOW_MODERATE_MESSAGES
	 *  - PHORUM_USER_ALLOW_MODERATE_USERS
	 *  - PHORUM_USER_ALLOW_FORUM_PROPERTIES
	 * </pre>
	 *
	 * @param int $p_permissions
	 * @return boolean
	 */
	function setRegisteredUserPermissions($p_permissions)
	{
		if (is_int($p_permissions)) {
			return $this->setProperty("reg_perms", $p_permissions);
		} else {
			return false;
		}
	} // fn setRegisteredUserPermissions


	/**
	 * Whether to count the number of times each message has been viewed.
	 *
	 * @return int
	 * 	 0 => "No"
	 *	 1 => "Yes, show views added to subject"
	 *	 2 => "Yes, show views as extra column"
	 */
	function countViews()
	{
		return $this->m_data["count_views"];
	} // fn countViews


	/**
	 * Set whether to count the number of times each message has been viewed.
	 *
	 * @param int $p_value
	 * 	 0 => "No"
	 *	 1 => "Yes, show views added to subject"
	 *	 2 => "Yes, show views as extra column"
	 * @return boolean
	 */
	function setCountViews($p_value)
	{
		if (is_numeric($p_value) && ($p_value >= 0) && ($p_value <= 2)) {
			return $this->setProperty('count_views', $p_value);
		} else {
			return false;
		}
	} // fn setCountViews


	/**
	 * Return TRUE if the forum is publicly visible.
	 *
	 * @return boolean
	 */
	function isVisible()
	{
		return $this->m_data["active"];
	} // fn isVisible


	/**
	 * Set whether the forum is visible to the public.
	 *
	 * @param boolean $p_value
	 * @return boolean
	 */
	function setIsVisible($p_value)
	{
		$p_value = $p_value ? "1" : "0";
		return $this->setProperty("active", $p_value);
	} // fn setIsVisible


	/**
	 * Check if the forum is moderated.
	 *
	 * @return boolean
	 */
	function isModerated()
	{
		return $this->m_data['moderation'];
	} // fn isModerated


	/**
	 * Set whether the forum is moderated or not.
	 * Use the constants PHORUM_MODERATE_OFF and PHORUM_MODERATE_ON
	 * to set the value.
	 *
	 * @param int $p_value
	 * @return boolean
	 */
	function setIsModerated($p_value)
	{
		$p_value = $p_value ? PHORUM_MODERATE_ON : PHORUM_MODERATE_OFF;
		return $this->setProperty('moderation', $p_value);
	} // fn setIsModerated


	/**
	 * Return TRUE if moderators will be emailed when a message
	 * is posted to this forum.
	 *
	 * @return boolean
	 */
	function emailModeratorsEnabled()
	{
		return $this->m_data['email_moderators'];
	} // fn emailModeratorsEnabled


	/**
	 * Set whether moderators should receive email when a message
	 * is posted.
	 *
	 * @param boolean $p_value
	 * @return boolean
	 */
	function setEmailModeratorsEnabled($p_value)
	{
		$p_value = $p_value ? PHORUM_EMAIL_MODERATOR_ON : PHORUM_EMAIL_MODERATOR_OFF;
		return $this->setProperty('email_moderators', $p_value);
	} // fn setEmailModeratorsEnabled


	/**
	 * Return TRUE if reverse threading is enabled.
	 *
	 * @return boolean
	 */
	function reverseThreadingEnabled()
	{
		return $this->m_data['reverse_threading'];
	} // fn reverseThreadingEnabled


	/**
	 * Get the number of messages posted to the forum.
	 *
	 * @return int
	 */
	function getNumMessages()
	{
		return $this->m_data['message_count'];
	} // fn getNumMessages


	/**
	 * Return the number of threads in this forum.
	 *
	 * @return int
	 */
	function getNumThreads()
	{
		return $this->m_data['thread_count'];
	} // fn getNumThreads


	/**
	 * Return the number of sticky messages in this forum.
	 *
	 * @return int
	 */
	function getNumSticky()
	{
		return $this->m_data['sticky_count'];
	} // fn getNumSticky


	/**
	 * Return the last time a message was posted as UNIX timestamp
	 * (number of seconds since January 1, 1970).
	 *
	 * @return int
	 */
	function getLastPostTime()
	{
		return $this->m_data['last_post_time'];
	} // fn getLastPostTime

} // class Phorum_forum

?>