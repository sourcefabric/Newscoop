<?PHP
/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable 
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT'] 
// is not defined in these cases.
if (!isset($g_documentRoot)) {
    $g_documentRoot = $_SERVER['DOCUMENT_ROOT'];
}
require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/DbObjectArray.php');

/**
 * @package Campsite
 */
class Event extends DatabaseObject {
	var $m_keyColumnNames = array('Id', 'IdLanguage');

	var $m_dbTableName = 'Events';
	
	var $m_columnNames = array('Id', 'IdLanguage', 'Name', 'Notify');
	
	function Event($p_id = null, $p_languageId = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['Id'] = $p_id;
		$this->m_data['IdLanguage'] = $p_languageId;
		if (!is_null($p_id) && !is_null($p_languageId)) {
			$this->fetch();
		}
	} // constructor
	
	
	/**
	 * @return int
	 */
	function getEventId()
	{
		return $this->getProperty('Id');
	} // fn getEventId
	
	
	/**
	 * @return string
	 */
	function getName()
	{
		return $this->getProperty('Name');
	} // fn getName
	
	
	/**
	 * @return array
	 */
	function GetEvents()
	{
		$tmpEvent =& new Event();
		$columns = implode(',', $tmpEvent->getColumnNames(true));
		$queryStr = "SELECT $columns FROM Events ORDER BY Id";
		$events = DbObjectArray::Create('Event', $queryStr);
		return $events;
	} // fn GetEvents
	
} // class Event

?>