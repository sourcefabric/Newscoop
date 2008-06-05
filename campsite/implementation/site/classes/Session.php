<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/classes/DatabaseObject.php');

/**
 * @package Campsite
 */
class Session extends DatabaseObject {
	var $m_keyColumnNames = array('id');
	var $m_keyIsAutoIncrement = true;
	var $m_dbTableName = 'Sessions';
	var $m_columnNames = array('id',
							   'start_time',
	                           'last_request',
	                           'user_id');

	public function __construct($p_id = null)
	{
        if (!is_null($p_id)) {
            $this->m_data['id'] = $p_id;
            $this->fetch();
        }
	} // constructor


	/**
	 * @return int
	 */
	public function getStartTime()
	{
		return $this->m_data['start_time'];
	} // fn getStartTime


	/**
	 * @return string
	 */
	public function getLastRequestTime()
	{
		return $this->m_data['last_request'];
	} // fn getLastRequestTime


	public function updateLastRequest()
	{
        global $g_ado_db;
	    $sql = "UPDATE Sessions SET last_request = NOW()";
	    return $g_ado_db->Execute($sql);
	}
} // class Session

?>