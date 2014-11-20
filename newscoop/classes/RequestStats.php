<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/RequestObject.php');

/**
 * @package Campsite
 */
class RequestStats extends DatabaseObject {
	var $m_keyColumnNames = array('object_id', 'date', 'hour');
	var $m_keyIsAutoIncrement = false;
	var $m_dbTableName = 'RequestStats';
	var $m_columnNames = array('object_id',
	                           'date',
	                           'hour',
	                           'request_count');

	/**
	 * @param integer $p_objectId
	 */
	public function __construct($p_objectId = null, $p_date = 'now',
	                            $p_hour = null)
	{
        if (!empty($p_objectId) && !empty($p_date)) {
            $this->m_data['object_id'] = $p_objectId;
            if (!strtotime($p_date)) {
            	return;
            }
            $this->m_data['date'] = date('Y-m-d', strtotime($p_date));
            if (empty($p_hour)) {
            	$date = getdate(strtotime($p_date));
            	$p_hour = $date['hours'];
            }
            $this->m_data['hour'] = $p_hour;
            $this->fetch();
        }
	} // constructor


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
    public function getDate()
    {
        return $this->m_data['date'];
    } // fn getDate


    /**
     * @return integer
     */
    public function getHour()
    {
        return $this->m_data['hour'];
    } // fn getHour


    /**
     * @return integer
     */
    public function getRequestCount()
    {
        return $this->m_data['request_count'];
    } // fn getRequestCount


    /**
     * @param integer $p_objectId
     *
     * @return string
     */
    public static function GetObjectRequestCount($p_objectId)
    {
    	global $g_ado_db;
    	$p_objectId = 0 + $p_objectId;
    	$sql = "SELECT SUM(request_count) FROM RequestStats "
    	     . "WHERE object_id = $p_objectId";
    	return $g_ado_db->GetOne($sql);
    }


    /**
     * @return integer
     */
    public function incrementRequestCount($p_count = 1)
    {
        global $g_ado_db;
        $p_count = 0 + $p_count;
        $sql = 'UPDATE ' . $this->m_dbTableName . ' '
             . "SET request_count = LAST_INSERT_ID(request_count + $p_count) "
             . "WHERE object_id = " . $g_ado_db->escape($this->m_data['object_id'])
             . "  AND date = " . $g_ado_db->escape($this->m_data['date'])
             . "  AND hour = " . $g_ado_db->escape($this->m_data['hour']);
        $success = $g_ado_db->Execute($sql);
        if ($success === false) {
        	return false;
        }

        $this->m_data['request_count'] = $g_ado_db->GetOne("SELECT LAST_INSERT_ID()");
        // Write the object to cache
        $this->writeCache();

        $requestObject = new RequestObject($this->m_data['object_id']);
        $requestObject->updateRequestCount();
        return $this->m_data['request_count'];
    }

} // class RequestStats

?>
