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

require_once($g_documentRoot.'/classes/AudioclipMetadataEntry.php');


/**
 * @package Campsite
 */
class AudioclipDatabaseMetadata {
    var $m_metaData = array();

    /**
     * Constructor
     */
    function AudioclipDatabaseMetadata($p_gunId = null)
    {
        if (!is_null($p_gunId)) {
            $this->m_gunId = $p_gunId;
            $this->fetch();
        }
    } // constructor


    /**
     * Fetch all metadata for the audioclip given.
     *
     * @param int $p_gunId
     *      The audioclip global unique identifier
     *
     * @return array $returnArray
     *      Array of AudioclipMetadataEntry objects
     */
    function fetch($p_gunId = null)
    {
        global $g_ado_db;

        if (!is_null($p_gunId)) {
            $this->m_gunId = $p_gunId;
        }
        if (is_null($this->m_gunId)) {
            return false;
        }

        $queryStr = "SELECT id FROM AudioclipMetadata
                     WHERE gunid = '".$this->m_gunId."' ORDER BY id";
        $rows = $g_ado_db->GetAll($queryStr);
        if (!$rows) {
            return false;
        }
        foreach ($rows as $row) {
            $tmpMetadataObj =& new AudioclipMetadataEntry($row['id']);
            $this->m_metaData[$tmpMetadataObj->getMetaTag()] =& $tmpMetadataObj;
        }
        return $this->m_metaData;
    } // fn fetch


    /**
     * Create metadata entries for a new Audioclip.
     *
     * @param string $p_metaData
     *      the XML metadata string
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    function create($p_metaData = null)
    {
        if (!is_array($p_metaData)) {
            return false;
        }

        $isError = false;
        foreach ($p_metaData as $metaDataEntry) {
            if (!$metaDataEntry->create()) {
                $isError = true;
                break;
            }
        }
        if ($isError) {
            foreach ($p_metaData as $metaDataEntry) {
                $metaDataEntry->delete();
            }
            return false;
        }
        return true;
    } // fn create

} // class AudioclipDatabaseMetadata

?>