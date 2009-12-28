<?PHP
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DbObjectArray.php');

/**
 * @package Campsite
 */
class Event extends DatabaseObject {
	var $m_keyColumnNames = array('Id', 'IdLanguage');

	var $m_dbTableName = 'Events';

	var $m_columnNames = array('Id', 'IdLanguage', 'Name', 'Notify');

	public function Event($p_id = null, $p_languageId = null)
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
	public function getEventId()
	{
		return $this->m_data['Id'];
	} // fn getEventId


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->m_data['Name'];
	} // fn getName


	/**
	 * @return array
	 */
	public static function GetEvents()
	{
		$tmpEvent = new Event();
		$columns = implode(',', $tmpEvent->getColumnNames(true));
		$queryStr = "SELECT $columns FROM Events ORDER BY Id";
		$events = DbObjectArray::Create('Event', $queryStr);
		return $events;
	} // fn GetEvents

} // class Event

?>