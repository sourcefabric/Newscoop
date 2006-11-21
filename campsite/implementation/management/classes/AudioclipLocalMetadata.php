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
require_once($g_documentRoot.'/classes/Translation.php');

/**
 * @package Campsite
 */
class AudioclipLocalMetadata extends DatabaseObject {
	var $m_keyColumnNames = array('id');
	var $m_keyIsAutoIncrement = true;
	var $m_dbTableName = 'AudioclipMetadata';
	var $m_columnNames = array('id',
							   'gunid',
							   'subject_ns',
							   'subject',
							   'predicate_ns',
							   'predicate',
							   'predicate_xml',
							   'object_ns',
							   'object');

	function AudioclipLocalMetadata($p_id = null)
	{
		if (!is_null($p_id)) {
			$this->m_data['id'] = $p_id;
			$this->fetch();
		}
	} // constructor


	function delete()
	{
		if (!$this->exists()) {
			return false;
		}

		// Delete the record in the database
		$success = parent::delete();

		return $success;
	} // fn delete


    function fetchAllMetadataByGunid($p_gunid)
    {
        global $g_ado_db;

        $queryStr = "SELECT * 
                     FROM AudioclipMetadata 
                     WHERE object_ns <> '_blank' AND gunid='$p_gunid'";
        $rows = $g_ado_db->GetAll($queryStr);
        if ($rows) {
            return $rows;
        }
        return null;
    } // fn fetchMetadataByGunid


    /**
     * Update values for all the audioclip metadata.
     *
     * @param array $p_mData
     */
    function editMetadata($p_mData)
    {
        //foreach($p_mData as $key => $val) {
            //$r = $this->__setMDataValue();
            //if (PEAR::isError($r)) {
                
            //}
        //}

        //return true;
    } // fn editMetadata


	/**
	 * @return int
	 */
	function getAudioclipMetadataId()
	{
		return $this->m_data['id'];
	} // fn getAudioclipMetadataId


    /**
     * @return int
     */
    function getGunid()
    {
        return $this->m_data['gunid'];
    } // fn getGunid


	/**
	 * @return string
	 */
	function getObjectName()
	{
		return $this->m_data['object'];
	} // fn getObjectName


    /**
     * Update value for a metadata record.
     */
    function __setMDataValue()
    {

    } // fn _setMDataValue


} // class AudioclipLocalMetadata

?>