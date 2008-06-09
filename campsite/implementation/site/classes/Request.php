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
class Request extends DatabaseObject {
	var $m_keyColumnNames = array('session_id', 'object_id');
	var $m_keyIsAutoIncrement = true;
	var $m_dbTableName = 'Requests';
	var $m_columnNames = array('session_id',
                               'object_id',
	                           'request_count',
	                           'last_request_time');

	public function __construct($p_sessionId = null, $p_objectId = null)
	{
        if (!is_null($p_sessionId) && !is_null($p_objectId)) {
            $this->m_data['session_id'] = $p_sessionId;
            $this->m_data['object_id'] = $p_objectId;
            $this->fetch();
        }
	} // constructor


	/**
	 * @return string
	 */
	public function getSessionId()
	{
		return $this->m_data['session_id'];
	} // fn getSessionId


    /**
     * @return integer
     */
    public function getObjectId()
    {
        return $this->m_data['object_id'];
    } // fn getObjectId


    /**
     * @return integer
     */
    public function getRequestCount() {
        return $this->m_data['request_count'];
    }


    /**
	 * @return string
	 */
	public function getLastRequestTime()
	{
		return $this->m_data['last_request_time'];
	} // fn getLastRequestTime
} // class Request

?>