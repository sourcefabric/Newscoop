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
	var $m_keyIsAutoIncrement = false;
	var $m_dbTableName = 'Sessions';
	var $m_columnNames = array('id',
							   'start_time',
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


	public function getUserId() {
	    return $this->m_data['user_id'];
	}
} // class Session

?>