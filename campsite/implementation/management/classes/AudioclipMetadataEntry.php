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
class AudioclipMetadataEntry extends DatabaseObject {
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

	function AudioclipMetadataEntry($p_id = null)
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


	/**
	 * @return int
	 */
	function getId()
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
    function getMetatag()
    {
        return strtolower($this->m_data['predicate']);
    } // fn getPredicate


    /**
     * @return string
     */
    function getMetatagNs()
    {
        return strtolower($this->m_data['predicate_ns']);
    } // fn getPredicateNs


	/**
	 * @return string
	 */
	function getValue()
	{
		return $this->m_data['object'];
	} // fn getObjectName


    /**
     * Update values for all the audioclip metadata.
     *
     * @param array $p_mData
     */
    function UpdateAllMetadata($p_mData)
    {
        //foreach($p_mData as $key => $val) {
            //$r = AudioclipMetadataEntry::__setMDataValue();
            //if (PEAR::isError($r)) {
                
            //}
        //}

        //return true;
    } // fn UpdateAllMetadata


    /**
     * Fetch all metadata for the audioclip given.
     *
     * @param int $p_gunid
     *
     * @return array $returnArray
     *      Array of AudioclipMetadataEntry objects
     */
    function FetchAllMetadataByGunid($p_gunid)
    {
        global $g_ado_db;

        $queryStr = "SELECT * 
                     FROM AudioclipMetadata 
                     WHERE object_ns <> '_blank' AND gunid='$p_gunid'";
        $rows = $g_ado_db->GetAll($queryStr);
        $returnArray = array();
        if ($rows) {
            foreach ($rows as $row) {
                $tmpMetadata =& new AudioclipMetadataEntry();
                $tmpMetadata->fetch($row);
                $returnArray[$tmpMetadata->getMetatag()] =& $tmpMetadata;
            }
        }
        return $returnArray;
    } // fn FetchAllMetadataByGunid


    /**
     * Update value for a metadata record.
     */
    function __setMDataValue()
    {

    } // fn __setMDataValue

} // class AudioclipMetadataEntry

?>