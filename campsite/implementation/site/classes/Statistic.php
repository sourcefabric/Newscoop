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
class Statistic extends DatabaseObject {
	var $m_keyColumnNames = array('object_id');
	var $m_keyIsAutoIncrement = true;
	var $m_dbTableName = 'Statistics';
	var $m_columnNames = array('object_id',
	                           'object_type_id',
	                           'count',
	                           'reset_time');

	public function __construct($p_objectId = null)
	{
        if (!is_null($p_objectId)) {
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
    public function getCount()
    {
        return $this->m_data['count'];
    } // fn getCount


    public function setCount($p_count)
    {
        return $this->setProperty('count', $p_count);
    }


    /**
	 * @return string
	 */
	public function getResetTime()
	{
		return $this->m_data['reset_time'];
	} // fn getLastRequestTime
} // class Statistic

?>