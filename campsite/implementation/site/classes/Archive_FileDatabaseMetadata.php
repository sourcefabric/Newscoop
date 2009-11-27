<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Archive_FileMetadataEntry.php');


/**
 * @package Campsite
 */
class Archive_FileDatabaseMetadata
{
    var $m_gunId = null;
    var $m_metaData = array();
    var $m_exists = false;


    /**
     * Constructor
     */
    public function __construct($p_gunId = null)
    {
        if (!is_null($p_gunId)) {
            $this->m_gunId = $p_gunId;
            $this->fetch();
        }
    } // constructor


    /**
     * Returns true if a file having this metadata exists
     *
     * @return boolean
     */
    public function exists()
    {
    	return $this->m_exists;
    }


    /**
     * Fetch all metadata for the file given.
     *
     * @param int $p_gunId
     *      The file global unique identifier
     *
     * @return array $returnArray
     *      Array of Archive_FileMetadataEntry objects
     */
    public function fetch($p_gunId = null)
    {
        global $g_ado_db;

        if (!is_null($p_gunId)) {
            $this->m_gunId = $p_gunId;
        }
        if (is_null($this->m_gunId)) {
	    $this->m_exists = false;
            return false;
        }

        $queryStr = "SELECT id FROM Archive_FileMetadata
                     WHERE gunid = '".$this->m_gunId."' ORDER BY id";
        $rows = $g_ado_db->GetAll($queryStr);
        if (!$rows) {
	    $this->m_exists = false;
            return false;
        }
        $this->m_exists = true;
        foreach ($rows as $row) {
            $tmpMetadataObj = new Archive_FileMetadataEntry($row['id']);
            $this->m_metaData[$tmpMetadataObj->getMetaTag()] = $tmpMetadataObj;
        }
        return $this->m_metaData;
    } // fn fetch


    /**
     * Create metadata entries for a new Archive_File.
     *
     * @param string $p_metaData
     *      the XML metadata string
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public function create($p_metaData = null)
    {
        if (!is_array($p_metaData)) {
	    $this->m_exists = false;
            return false;
        }

        $isError = false;
        $gunId = null;
        foreach ($p_metaData as $metaDataEntry) {
	    $gunId = $metaDataEntry->getGunId();
            if (!$metaDataEntry->create()) {
                $isError = true;
                break;
            }
        }
        if ($isError) {
            foreach ($p_metaData as $metaDataEntry) {
                $metaDataEntry->delete();
            }
	    $this->m_exists = false;
            return false;
        }
        $this->m_gunId = $gunId;
        $this->m_metaData = $p_metaData;
        $this->m_exists = true;
        return true;
    } // fn create


    /**
     * Deletes all the metadata for the archive file.
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public function delete()
    {
        global $g_ado_db;

        if (is_null($this->m_gunId)) {
            return false;
        }

        $queryStr = "DELETE FROM Archive_FileMetadata WHERE gunid = '".$g_ado_db->escape($this->m_gunId)."'";
        if (!$g_ado_db->Execute($queryStr)) {
            return false;
        }
        $this->m_gunId = null;
        $this->m_metaData = array();
        $this->m_exists = false;
        return true;
    } // fn delete


    /**
     * Updates the file metadata in local database.
     *
     * @param array $p_metaData
     *      An array of Archive_FileMetadataEntry objects
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public function update($p_metaData)
    {
        if (!is_array($p_metaData)) {
	    $this->m_exists = false;
            return false;
        }
        $newDataKeys = array_keys($p_metaData);
        $oldDataKeys = array_keys($this->m_metaData);
        $metaDataToDelete = array_diff($oldDataKeys, $newDataKeys);
        foreach ($this->m_metaData as $metadataEntry) {
            if (in_array($metadataEntry->getMetatag(), $metaDataToDelete)) {
                $metadataEntry->delete();
            }
        }
        foreach ($p_metaData as $metadataEntry) {
            $attributes = array();
            if ($metadataEntry->exists()) {
                $attributes['id'] = $metadataEntry->getId();
                $attributes['gunid'] = $metadataEntry->getGunId();
                $attributes['predicate_ns'] = $metadataEntry->getMetatagNS();
                $attributes['predicate'] = $metadataEntry->getMetatagName();
                $attributes['object'] = $metadataEntry->getValue();
                $currMetadataEntry = new Archive_FileMetadataEntry($metadataEntry->getId());
                if (!$currMetadataEntry->update($attributes)) {
                    $isError = true;
                    break;
                }
            } else {
                if (!$metadataEntry->create()) {
                    $isError = true;
                    break;
                }
            }
        }
        if ($isError) {
            return false;
        }
        return true;
    } // fn update


    /**
     * Checks whether the file is in use by multiple articles.
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public function inUse()
    {
        global $g_ado_db;

	// TODO: define how we are gonna deal with this
        $queryStr = "SELECT COUNT(*) AS count FROM ArticleArchiveFiles
                     WHERE fk_audioclip_gunid != '".$g_ado_db->escape($this->m_gunId)."'";
        $row = $g_ado_db->GetRow($queryStr);
        return $row['count'] > 0;
    } // fn inUse

} // class Archive_FileDatabaseMetadata

?>