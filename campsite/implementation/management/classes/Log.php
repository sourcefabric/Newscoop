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
	
	
	function getTimeStamp()
	{
		return $this->getProperty('TStamp');
	} // fn getTimeStamp
	
	
	function getUserName()
	{
		return $this->getProperty('User');
	} // fn getUserName
	
	
	function getText()
	{
		return $this->getProperty('Text');
	} // fn getText
	
	
	function getEventId()
	{
		return $this->getProperty('IdEvent');
	} // fn getEventId
	
	
	function GetNumLogs($p_eventId = null)
	{
		global $Campsite;
		$queryStr = 'SELECT COUNT(*) FROM Log';
		if (!is_null($p_eventId)) {
			$queryStr .= " WHERE IdEvent=$p_eventId";
		}
		$total = $Campsite['db']->GetOne($queryStr);
		return $total;
	} // fn GetNumLogs
	
	
	function GetLogs($p_eventId = null, $p_sqlOptions = null)
	{
		if (is_null($p_sqlOptions) || !isset($p_sqlOptions['ORDER BY'])) {
			$p_sqlOptions['ORDER BY'] = array('TStamp' => 'DESC');
		}
		$constraints = array();
		if (!is_null($p_eventId)) {
			$constraints[] = array('IdEvent', $p_eventId);
		}
		return DatabaseObject::Search('Log', $constraints, $p_sqlOptions);
	} // fn GetLogs
	
} // class Log

?>