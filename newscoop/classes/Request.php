<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');

/**
 * @package Campsite
 */
class Request extends DatabaseObject {
	var $m_keyColumnNames = array('session_id', 'object_id');
	var $m_keyIsAutoIncrement = false;
	var $m_dbTableName = 'Requests';
	var $m_columnNames = array('session_id',
                               'object_id',
	                           'last_stats_update');

	/**
	 * @param integer $p_sessionId
	 * @param integer $p_objectId
	 */
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


    public function setLastStatsUpdate($p_time = null)
    {
    	if (empty($p_time)) {
    		$p_time = date('Y-m-d G:i:s');
    	}
    	return $this->setProperty('last_stats_update', $p_time);
    }


    /**
     * @return string
     */
    public function getLastStatsUpdate()
    {
    	return $this->m_data['last_stats_update'];
    }


    public function isInStats()
    {
        $lastUpdateTime = strtotime($this->getLastStatsUpdate());
        return $lastUpdateTime != 0;
//    	$currentTime = date('Y-m-d G');
//    	$lastUpdateTime = date('Y-m-d G', strtotime($this->getLastStatsUpdate()));
//    	return $currentTime == $lastUpdateTime;
    }
} // class Request

?>