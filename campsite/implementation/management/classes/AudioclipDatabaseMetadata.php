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

require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/AudioclipMetadataEntry.php');


/**
 * @package Campsite
 */
class AudioclipDatabaseMetadata {
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


    /**
     * Constructor
     */
    function AudioclipDatabaseMetadata($p_gunId = null)
    {
        if (!is_null($p_gunId)) {
            $this->m_gunId = $p_gunId;
        }
    } // constructor


    /**
     * Fetch all metadata for the audioclip given.
     *
     * @param int $p_gunid
     *
     * @return array $returnArray
     *      Array of AudioclipMetadataEntry objects
     */
    function fetch()
    {
        global $g_ado_db;

        $queryStr = "SELECT CONCAT(predicate_ns, ':', predicate) AS tagName,
                            object AS tagValue
                     FROM AudioclipMetadata
                     WHERE object_ns <> '_blank' AND gunid = '"
                    .$this->m_gunId."'";
        $rows = $g_ado_db->GetAll($queryStr);
        $metaData = array();
        if ($rows) {
            foreach ($rows as $row) {
                $tagName = strtoupper($row['tagName']);
                $tagValue = $row['tagValue'];
                $tmpMetadataObj =& new AudioclipMetadataEntry($tagName, $tagValue);
                $metaData[$tagName] =& $tmpMetadataObj;
            }
        }
        return $metaData;
    } // fn fetch
    
    
    function create($p_metaData)
    {
    	if (!is_array($p_metaData)) {
    		return false;
    	}
    	
    }


    /**
     * TO BE DONE
     */
    function write()
    {

    } // fn write


    /**
     * TO BE DONE
     * We can use insertMetadataEl() in storageServer/var/MetaData.php
     * as base to build this method
     */
    function __insertMetadataElement()
    {

    } // fn __insertMetadataValue


    /**
     * TO BE DONE
     * We can use setMetadataEl() in storageServer/var/MetaData.php
     * as base to build this method
     */
    function __setMetadataElement()
    {

    } // fn __setMetadataValue

} // class AudioclipDatabaseMetadata

?>