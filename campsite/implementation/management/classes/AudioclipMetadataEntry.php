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
							   'predicate_ns',
							   'predicate',
							   'object');

	function AudioclipMetadataEntry($p_data = null)
	{
		if (is_null($p_data)) {
			return;
		}
		if (is_numeric($p_data)) {
			$this->m_data['id'] = $p_data;
			$this->fetch();
		}
		if (is_array($p_data)) {
			$this->fetch($p_data);
		}
	} // constructor
	
	
	function fetch($p_recordSet = null)
	{
		global $g_ado_db;
		
		if (!is_null($p_recordSet) && is_array($p_recordSet)) {
			$this->m_data = $p_recordSet;
		}
		if (isset($this->m_data['id'])) {
			return parent::fetch();
		}
		if (!isset($this->m_data['gunid']) || !isset($this->m_data['predicate_ns'])
				|| !isset($this->m_data['predicate']) || !isset($this->m_data['object'])) {
			return false;
		}
		$sql = 'SELECT * FROM `'.$g_ado_db->escape($this->m_dbTableName)."`"
			." WHERE gunid = '".$g_ado_db->escape($this->m_data['gunid'])."'"
			." AND predicate_ns = '".$g_ado_db->escape($this->m_data['predicate_ns'])."'"
			." AND predicate = '".$g_ado_db->escape($this->m_data['predicate'])."'";
		$resultSet = $g_ado_db->GetRow($sql);
		if ($resultSet) {
			$this->m_exists = true;
		} else {
			$this->m_exists = false;
		}
		return $this->m_exists;
	}
	
	
	function delete()
	{
		if (!$this->exists()) {
			return false;
		}

		return parent::delete();
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
        return $this->getMetatagNs().':'.$this->getMetatagName();
    } // fn getPredicate


    /**
     * @return string
     */
    function getMetatagName()
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
	 * @return boolean
	 */
	function IsValidNamespace($p_metatag)
	{
		$metatag = strtolower($p_metatag);
		$namespace = strtok($metatag, ':');
		return in_array($namespace, array('dc', 'ls', 'dcterms'));
	} // fn IsValidNamespace


 	/**
	 * @return string
	 */
	function GetTagNS($p_tag)
	{
		if (!AudioclipMetadataEntry::IsValidNamespace($p_tag)) {
			return null;
		}
		return strtok(strtolower($p_tag), ':');
	} // fn GetTagNS
	
	
	/**
	 * @return string
	 */
	function GetTagName($p_tag)
	{
		$tok = strtok(strtolower($p_tag), ':');
		if ($tok !== false) {
			$tok = strtok(':');
		}
		return $tok;
	} // fn GetTagName
	
	
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
                $returnArray[$tmpMetadata->getMetatagName()] =& $tmpMetadata;
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