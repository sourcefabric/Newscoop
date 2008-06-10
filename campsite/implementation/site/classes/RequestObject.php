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
class RequestObject extends DatabaseObject {
	var $m_keyColumnNames = array('object_id');
	var $m_keyIsAutoIncrement = true;
	var $m_dbTableName = 'RequestObjects';
	var $m_columnNames = array('object_id',
	                           'object_type_id',
	                           'request_count',
	                           'last_update_time');

	public function __construct($p_objectId = null)
	{
        if (!is_null($p_objectId) && !empty($p_objectId)) {
            $this->m_data['object_id'] = $p_objectId;
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
    public function getObjectTypeId()
    {
        return $this->m_data['object_type_id'];
    } // fn getObjectTypeId


    /**
     * @return integer
     */
    public function getRequestCount()
    {
        return $this->m_data['request_count'];
    } // fn getRequestCount


    /**
     * @return integer
     */
    public function incrementRequestCount($p_count)
    {
        global $g_ado_db;
        $sql = "UPDATE " . $this->m_dbTableName . " SET request_count = LAST_INSERT_ID(request_count + 1)";
        $g_ado_db->Execute($sql);
        $this->m_data['request_count'] = $g_ado_db->GetOne("SELECT LAST_INSERT_ID()");
        return $this->m_data['request_count'];
    }


    /**
	 * @return string
	 */
	public function getLastUpdateTime()
	{
		return $this->m_data['last_update_time'];
	} // fn getLastUpdateTime
} // class RequestObject

?>