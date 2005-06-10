<?php
/**
 * @package Campsite
 */


/**
 * @package Campsite
 */
class Log extends DatabaseObject {
	var $m_keyColumnNames = array('IdEvent');
	var $m_keyIsAutoIncrement = false;
	var $m_dbTableName = 'Log';
	var $m_columnNames = array(
		'TStamp', 
		'IdEvent', 
		'User', 
		'Text');
	
	/**
	 * This is a static function.
	 * Write a message to the log table.
	 *
	 * @param string $p_text
	 * @param string $p_userName
	 * @param int $p_eventId
	 *
	 * @return void
	 */
	function Message($p_text, $p_userName = '', $p_eventId = 0) 
	{
		global $Campsite;
		$queryStr = "INSERT INTO Log SET TStamp=NOW(), IdEvent=$p_eventId, User='$p_userName', Text='".mysql_real_escape_string($p_text)."'";
		$Campsite['db']->Execute($queryStr);
	} // fn Message
	
} // class Log

?>